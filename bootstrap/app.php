<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.school.license' => \App\Http\Middleware\CheckSchoolLicense::class,
            'check.school.license.api' => \App\Http\Middleware\CheckSchoolLicenseApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 419 CSRF Token Mismatch hatası için özel işleme
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Oturum süreniz dolmuş. Lütfen sayfayı yenileyip tekrar deneyin.',
                    'error' => 'CSRF token mismatch'
                ], 419);
            }
            
            // Login sayfasındaysa, hata mesajıyla birlikte geri dön
            if ($request->is('login') || $request->is('register')) {
                return redirect()->back()
                    ->withInput($request->except('_token', 'password'))
                    ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar deneyin.');
            }
            
            // Diğer sayfalarda login sayfasına yönlendir
            return redirect()->route('login')
                ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar giriş yapın.');
        });
    })->create();
