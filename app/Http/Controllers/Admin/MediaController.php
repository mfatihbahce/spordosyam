<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaTarget;
use App\Models\Branch;
use App\Models\SportBranch;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $media = Media::where('school_id', $schoolId)
            ->with(['uploadedBy', 'targets'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.media.index', compact('media'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $branches = Branch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $sportBranches = SportBranch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $students = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        
        return view('admin.media.create', compact('branches', 'sportBranches', 'classes', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,mp4,avi,mov|max:10240', // 10MB max
            'target_type' => 'required|in:all,branch,sport_branch,class,student',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'integer',
        ]);

        $schoolId = Auth::user()->school_id;

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
            'uploader_type' => 'admin',
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

        return redirect()->route('admin.media.index')
            ->with('success', 'Medya başarıyla yüklendi.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        
        if (!$user || !$schoolId) {
            abort(403, 'Okul bilginiz bulunamadı.');
        }
        
        // Medyayı manuel olarak yükle ve school_id kontrolü yap
        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();
        
        if (!$media) {
            abort(404, 'Medya bulunamadı veya bu medyaya erişim yetkiniz yok.');
        }
        
        $media->load(['uploadedBy', 'targets']);
        
        return view('admin.media.show', compact('media'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        
        if (!$user || !$schoolId) {
            abort(403);
        }
        
        // Medyayı manuel olarak yükle ve school_id kontrolü yap
        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
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

        return redirect()->route('admin.media.index')
            ->with('success', 'Medya başarıyla silindi.');
    }
}
