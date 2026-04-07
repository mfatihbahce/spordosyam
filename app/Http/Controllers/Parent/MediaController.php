<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15, 1);
            return view('parent.media.index', [
                'media' => $emptyPaginator
            ])->with('error', 'Veli bilgileriniz bulunamadı.');
        }
        
        // Veli'nin öğrencileri
        $studentIds = $parent->students->pluck('id');
        
        // Öğrencilerin sınıfları
        $classIds = $parent->students->pluck('class_id')->filter()->unique();
        
        // Veli'nin görebileceği medyalar: hedefi olmayan (herkese açık) veya öğrencilerine/sınıflarına atanmış olanlar
        $media = Media::where('school_id', $schoolId)
            ->where(function($query) use ($studentIds, $classIds) {
                $query->whereDoesntHave('targets') // Hedefi olmayan (herkese açık) medyalar
                    ->orWhereHas('targets', function($q) use ($studentIds, $classIds) {
                        $q->where(function($q2) use ($studentIds) {
                            $q2->where('target_type', 'student')
                               ->whereIn('target_id', $studentIds);
                        })->orWhere(function($q2) use ($classIds) {
                            $q2->where('target_type', 'class')
                               ->whereIn('target_id', $classIds);
                        });
                    });
            })
            ->with(['uploadedBy', 'targets'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('parent.media.index', compact('media'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        $parent = $user->parent;
        
        // Kullanıcı kontrolü
        if (!$user) {
            abort(401, 'Giriş yapmanız gerekiyor.');
        }
        
        if (!$schoolId) {
            abort(403, 'Okul bilginiz bulunamadı. Lütfen yöneticinizle iletişime geçin.');
        }
        
        if (!$parent) {
            abort(403, 'Veli bilgileriniz bulunamadı.');
        }
        
        // Medyayı manuel olarak yükle ve school_id kontrolü yap
        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();
        
        if (!$media) {
            abort(404, 'Medya bulunamadı veya bu medyaya erişim yetkiniz yok.');
        }
        
        // İlişkileri yükle
        $media->load(['uploadedBy', 'targets']);
        
        // Veli'nin bu medyayı görebilme yetkisi var mı kontrol et
        $studentIds = $parent->students->pluck('id');
        $classIds = $parent->students->pluck('class_id')->filter()->unique();
        
        $canView = $media->targets->count() === 0 || // Hedefi yoksa herkes görebilir
                   $media->targets->where('target_type', 'student')->whereIn('target_id', $studentIds)->count() > 0 ||
                   $media->targets->where('target_type', 'class')->whereIn('target_id', $classIds)->count() > 0;
        
        if (!$canView) {
            abort(403, 'Bu medyayı görüntüleme yetkiniz bulunmamaktadır.');
        }
        
        return view('parent.media.show', compact('media'));
    }
}
