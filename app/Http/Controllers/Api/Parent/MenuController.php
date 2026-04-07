<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Sidebar menü yapısı - platformdaki menü ile birebir aynı.
     * Mobil uygulama bu yapıyı kullanarak menüyü render eder.
     */
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $school = $parent->school;
        $makeupClassEnabled = $school && $school->makeup_class_enabled;

        $menu = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'chart-line',
                'route' => 'parent/dashboard',
                'section' => null,
            ],
            [
                'section' => 'Öğrenci',
                'items' => [
                    [
                        'key' => 'students',
                        'label' => 'Çocuğum',
                        'icon' => 'user-graduate',
                        'route' => 'parent/students',
                    ],
                ],
            ],
            [
                'section' => 'Takip',
                'items' => [
                    [
                        'key' => 'attendances',
                        'label' => 'Yoklamalar',
                        'icon' => 'check-circle',
                        'route' => 'parent/attendances',
                    ],
                    [
                        'key' => 'progress',
                        'label' => 'Gelişim Notları',
                        'icon' => 'chart-line',
                        'route' => 'parent/progress',
                    ],
                ],
            ],
        ];

        if ($makeupClassEnabled) {
            $menu[] = [
                'section' => 'Telafi',
                'items' => [
                    [
                        'key' => 'makeup-sessions',
                        'label' => 'Telafi Dersleri',
                        'icon' => 'calendar-plus',
                        'route' => 'parent/makeup-sessions',
                    ],
                ],
            ];
        }

        $menu[] = [
            'section' => 'İçerik',
            'items' => [
                [
                    'key' => 'media',
                    'label' => 'Paylaşımlar',
                    'icon' => 'images',
                    'route' => 'parent/media',
                ],
            ],
        ];

        $menu[] = [
            'section' => 'Ödemeler',
            'items' => [
                [
                    'key' => 'payments',
                    'label' => 'Aidatlarım',
                    'icon' => 'money-bill-wave',
                    'route' => 'parent/payments',
                ],
                [
                    'key' => 'payments-history',
                    'label' => 'Ödeme Geçmişi',
                    'icon' => 'history',
                    'route' => 'parent/payments/history',
                ],
                [
                    'key' => 'invoices',
                    'label' => 'Faturalar',
                    'icon' => 'file-invoice',
                    'route' => 'parent/invoices',
                ],
            ],
        ];

        $menu[] = [
            'section' => 'İletişim',
            'items' => [
                [
                    'key' => 'messages',
                    'label' => 'Mesajlar',
                    'icon' => 'comments',
                    'route' => 'parent/messages',
                ],
            ],
        ];

        $menu[] = [
            'section' => 'Hesap',
            'items' => [
                [
                    'key' => 'profile',
                    'label' => 'Profil',
                    'icon' => 'user',
                    'route' => 'parent/profile',
                ],
            ],
        ];

        return response()->json([
            'menu' => $menu,
            'makeup_class_enabled' => $makeupClassEnabled,
        ]);
    }
}
