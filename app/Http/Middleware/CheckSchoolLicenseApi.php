<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolLicenseApi
{
    /**
     * API istekleri için lisans kontrolü. Süre dolmuşsa 403 JSON döner.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $school = $user->getSchoolForLicense();
        if (!$school) {
            return $next($request);
        }

        if ($school->isLicenseExpired()) {
            return response()->json([
                'message' => 'Okul lisans süresi dolmuş. Lütfen yöneticinizle iletişime geçin.',
            ], 403);
        }

        return $next($request);
    }
}
