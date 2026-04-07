<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Helpers\EnvHelper;
use App\Services\IyzicoService;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'iyzico_api_key' => env('IYZICO_API_KEY', ''),
            'iyzico_secret_key' => env('IYZICO_SECRET_KEY', ''),
            'iyzico_base_url' => env('IYZICO_BASE_URL', 'https://api.iyzipay.com'),
            'default_commission_rate' => env('DEFAULT_COMMISSION_RATE', 5.00),
        ];

        return view('superadmin.payment-settings.index', compact('settings'));
    }

    public function testConnection()
    {
        try {
            $iyzicoService = new IyzicoService();
            $testResults = $iyzicoService->testConnection();
            
            return response()->json($testResults);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Test sırasında beklenmeyen hata: ' . $e->getMessage()],
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'iyzico_api_key' => 'nullable|string',
            'iyzico_secret_key' => 'nullable|string',
            'iyzico_base_url' => 'required|url',
            'default_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        // .env dosyasını güncelle
        $envData = [];
        
        if ($request->filled('iyzico_api_key')) {
            $envData['IYZICO_API_KEY'] = $request->iyzico_api_key;
        }
        
        if ($request->filled('iyzico_secret_key')) {
            $envData['IYZICO_SECRET_KEY'] = $request->iyzico_secret_key;
        }
        
        $envData['IYZICO_BASE_URL'] = $request->iyzico_base_url;
        $envData['DEFAULT_COMMISSION_RATE'] = $request->default_commission_rate;

        if (EnvHelper::updateEnvMultiple($envData)) {
            return redirect()->route('superadmin.payment-settings.index')
                ->with('success', 'Ödeme ayarları başarıyla güncellendi.');
        } else {
            return redirect()->route('superadmin.payment-settings.index')
                ->with('error', 'Ayarlar güncellenirken bir hata oluştu. Lütfen .env dosyasını manuel olarak kontrol edin.');
        }
    }
}
