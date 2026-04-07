<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaTarget;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15, 1);
            return view('coach.media.index', [
                'media' => $emptyPaginator
            ])->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }
        
        $coachId = $coach->id;
        
        // Antrenörün sınıfları
        $classes = ClassModel::where('coach_id', $coachId)->pluck('id');
        
        // Antrenörün sınıflarındaki öğrenciler
        $students = Student::whereIn('class_id', $classes)->pluck('id');
        
        // Antrenörün görebileceği medyalar: kendi yükledikleri veya sınıflarına/öğrencilerine atanmış olanlar
        $media = Media::where('school_id', $schoolId)
            ->where(function($query) use ($coachId, $classes, $students) {
                $query->where('uploaded_by', Auth::id())
                    ->orWhereHas('targets', function($q) use ($classes, $students) {
                        $q->where(function($q2) use ($classes) {
                            $q2->where('target_type', 'class')
                               ->whereIn('target_id', $classes);
                        })->orWhere(function($q2) use ($students) {
                            $q2->where('target_type', 'student')
                               ->whereIn('target_id', $students);
                        });
                    })
                    ->orWhereDoesntHave('targets'); // Hedefi olmayan (herkese açık) medyalar
            })
            ->with(['uploadedBy', 'targets'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('coach.media.index', compact('media'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            abort(403, 'Antrenör bilgileriniz bulunamadı.');
        }
        
        $coachId = $coach->id;
        
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('coach_id', $coachId)
            ->where('is_active', true)
            ->get();
        
        $students = Student::whereIn('class_id', $classes->pluck('id'))
            ->where('is_active', true)
            ->get();
        
        return view('coach.media.create', compact('classes', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,mp4,avi,mov|max:10240',
            'target_type' => 'required|in:all,class,student',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'integer',
        ]);

        $schoolId = Auth::user()->school_id;
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            abort(403, 'Antrenör bilgileriniz bulunamadı.');
        }
        
        $coachId = $coach->id;

        // Dosya yükleme - public/uploads/media/ klasörüne kaydet
        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $uploadPath = public_path('uploads/media/' . $schoolId);

        // MIME tipi ve boyut move() ÖNCE alınmalı (move sonrası temp dosya silinir)
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $clientOriginalName = $file->getClientOriginalName();

        // Klasör yoksa oluştur
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $fileName);
        $filePath = 'uploads/media/' . $schoolId . '/' . $fileName;

        // Dosya tipini belirle
        $type = 'image';
        if (str_contains($mimeType, 'pdf')) {
            $type = 'pdf';
        } elseif (str_contains($mimeType, 'video')) {
            $type = 'video';
        }

        // Medya kaydı oluştur
        $media = Media::create([
            'school_id' => $schoolId,
            'uploaded_by' => Auth::id(),
            'uploader_type' => 'coach',
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $type,
            'file_path' => $filePath,
            'file_name' => $clientOriginalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'visibility' => 'public',
        ]);

        // Hedefleri kaydet
        if ($validated['target_type'] !== 'all' && !empty($validated['target_ids'])) {
            foreach ($validated['target_ids'] as $targetId) {
                MediaTarget::create([
                    'media_id' => $media->id,
                    'target_type' => $validated['target_type'],
                    'target_id' => $targetId,
                ]);
            }
        }

        return redirect()->route('coach.media.index')
            ->with('success', 'Medya başarıyla yüklendi.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        $coach = $user->coach;
        
        if (!$user) {
            abort(401, 'Giriş yapmanız gerekiyor.');
        }
        
        if (!$schoolId) {
            abort(403, 'Okul bilginiz bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }
        
        if (!$coach) {
            abort(403, 'Antrenör bilgileriniz bulunamadı.');
        }
        
        // Medyayı manuel olarak yükle ve school_id kontrolü yap
        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();
        
        if (!$media) {
            abort(404, 'Medya bulunamadı veya bu medyaya erişim yetkiniz yok.');
        }
        
        $coachId = $coach->id;
        
        // İlişkileri yükle
        $media->load(['uploadedBy', 'targets']);
        
        // Antrenörün sınıfları
        $classes = ClassModel::where('coach_id', $coachId)->pluck('id');
        
        // Antrenörün sınıflarındaki öğrenciler
        $students = Student::whereIn('class_id', $classes)->pluck('id');
        
        // Antrenörün bu medyayı görebilme yetkisi var mı kontrol et
        $canView = $media->uploaded_by === Auth::id() || // Kendi yüklediği
                   $media->targets->count() === 0 || // Hedefi yoksa herkes görebilir
                   $media->targets->where('target_type', 'class')->whereIn('target_id', $classes)->count() > 0 ||
                   $media->targets->where('target_type', 'student')->whereIn('target_id', $students)->count() > 0;
        
        if (!$canView) {
            abort(403, 'Bu medyayı görüntüleme yetkiniz bulunmamaktadır.');
        }
        
        return view('coach.media.show', compact('media'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        
        if (!$user || !$schoolId) {
            abort(403);
        }
        
        // Medyayı manuel olarak yükle ve yetki kontrolü yap
        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->where('uploaded_by', $user->id)
            ->first();
        
        if (!$media) {
            abort(404, 'Medya bulunamadı veya bu medyayı silme yetkiniz yok.');
        }
        
        // Dosyayı sil
        $fullPath = public_path($media->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        $media->delete();

        return redirect()->route('coach.media.index')
            ->with('success', 'Medya başarıyla silindi.');
    }
}
