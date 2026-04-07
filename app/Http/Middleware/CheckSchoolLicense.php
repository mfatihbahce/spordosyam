<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolLicense
{
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
            return redirect()->route('license-expired');
        }

        return $next($request);
    }
}
