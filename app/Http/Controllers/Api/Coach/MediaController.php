<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    private function getSchoolId(): ?int
    {
        $user = Auth::user();
        return $user->coach?->school_id ?? $user->school_id;
    }

    public function index()
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $media = Media::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/');
        return response()->json([
            'media' => $media->map(function ($m) use ($baseUrl) {
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'description' => $m->description,
                    'type' => $m->type,
                    'file_url' => $m->file_path ? media_url($m->file_path) : null,
                    'file_url_secure' => $m->file_path ? $baseUrl . '/api/coach/media/' . $m->id . '/file' : null,
                    'created_at' => $m->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function show($id)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();

        if (!$media) {
            return response()->json(['message' => 'Medya bulunamadı.'], 404);
        }

        $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/');
        $fileUrl = $media->file_path ? media_url($media->file_path) : null;
        $fileUrlSecure = $media->file_path ? $baseUrl . '/api/coach/media/' . $media->id . '/file' : null;

        return response()->json([
            'id' => $media->id,
            'title' => $media->title,
            'description' => $media->description,
            'type' => $media->type,
            'file_url' => $fileUrl,
            'file_url_secure' => $fileUrlSecure,
            'created_at' => $media->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Medya dosyasını token ile sunar. Mobil uygulamada Authorization header ile erişilir.
     */
    public function file($id): StreamedResponse
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            abort(404, 'Antrenör bulunamadı.');
        }

        $media = Media::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();

        if (!$media || !$media->file_path) {
            abort(404, 'Medya bulunamadı.');
        }

        $fullPath = public_path($media->file_path);
        if (!file_exists($fullPath)) {
            abort(404, 'Dosya bulunamadı.');
        }

        $mimeType = $media->mime_type ?: 'application/octet-stream';

        return response()->streamDownload(function () use ($fullPath) {
            $stream = fopen($fullPath, 'r');
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, basename($media->file_path), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($media->file_path) . '"',
        ]);
    }
}

