<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $schoolId = $parent->school_id;
        $studentIds = $parent->students->pluck('id');
        $classIds = $parent->students->pluck('class_id')->filter()->unique();

        $media = Media::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where(function ($query) use ($studentIds, $classIds) {
                $query->whereDoesntHave('targets')
                    ->orWhereHas('targets', function ($q) use ($studentIds, $classIds) {
                        $q->where(function ($q2) use ($studentIds) {
                            $q2->where('target_type', 'student')
                               ->whereIn('target_id', $studentIds);
                        })->orWhere(function ($q2) use ($classIds) {
                            $q2->where('target_type', 'class')
                               ->whereIn('target_id', $classIds);
                        });
                    });
            })
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
                    'file_url_secure' => $m->file_path ? $baseUrl . '/api/parent/media/' . $m->id . '/file' : null,
                    'created_at' => $m->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function show($id)
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $schoolId = $parent->school_id;
        $media = Media::where('id', $id)
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->first();

        if (!$media) {
            return response()->json(['message' => 'Medya bulunamadı.'], 404);
        }

        $media->load('targets');

        $studentIds = $parent->students->pluck('id');
        $classIds = $parent->students->pluck('class_id')->filter()->unique();

        $canView = $media->targets->count() === 0 ||
            $media->targets->where('target_type', 'student')->whereIn('target_id', $studentIds)->count() > 0 ||
            $media->targets->where('target_type', 'class')->whereIn('target_id', $classIds)->count() > 0;

        if (!$canView) {
            return response()->json(['message' => 'Bu medyayı görüntüleme yetkiniz yok.'], 403);
        }

        $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/');
        $fileUrl = $media->file_path ? media_url($media->file_path) : null;
        $fileUrlSecure = $media->file_path ? $baseUrl . '/api/parent/media/' . $media->id . '/file' : null;

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
     * Medya dosyasını token ile sunar. Mobil uygulamada Image bileşeni
     * Authorization header desteklemediği için bu endpoint kullanılır.
     * İstek: GET /api/parent/media/{id}/file
     * Header: Authorization: Bearer {token}
     */
    public function file($id): StreamedResponse
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            abort(404, 'Veli bulunamadı.');
        }

        $schoolId = $parent->school_id;
        $media = Media::where('id', $id)
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->first();

        if (!$media || !$media->file_path) {
            abort(404, 'Medya bulunamadı.');
        }

        $fullPath = public_path($media->file_path);
        if (!file_exists($fullPath)) {
            abort(404, 'Dosya bulunamadı.');
        }

        $studentIds = $parent->students->pluck('id');
        $classIds = $parent->students->pluck('class_id')->filter()->unique();
        $media->load('targets');

        $canView = $media->targets->count() === 0 ||
            $media->targets->where('target_type', 'student')->whereIn('target_id', $studentIds)->count() > 0 ||
            $media->targets->where('target_type', 'class')->whereIn('target_id', $classIds)->count() > 0;

        if (!$canView) {
            abort(403, 'Bu medyayı görüntüleme yetkiniz yok.');
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

