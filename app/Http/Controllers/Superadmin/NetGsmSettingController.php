<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\NetGsmService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class NetGsmSettingController extends Controller
{
    public function index()
    {
        $credentials = [
            'netgsm_username' => SiteSetting::get('netgsm_username', ''),
            'netgsm_password' => SiteSetting::get('netgsm_password', ''),
            'netgsm_msgheader' => SiteSetting::get('netgsm_msgheader', ''),
        ];

        $enabledTypes = SmsNotificationService::getEnabledTypes();
        $typesConfig = SmsNotificationService::getTypesConfig();

        $veliTypes = array_filter($typesConfig, fn($c) => ($c['group'] ?? '') === 'veli');
        $antrenorTypes = array_filter($typesConfig, fn($c) => ($c['group'] ?? '') === 'antrenor');

        return view('superadmin.netgsm-settings.index', compact(
            'credentials',
            'enabledTypes',
            'veliTypes',
            'antrenorTypes'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'netgsm_username' => 'nullable|string|max:255',
            'netgsm_password' => 'nullable|string|max:255',
            'netgsm_msgheader' => 'nullable|string|max:11',
        ]);

        SiteSetting::set('netgsm_username', $request->input('netgsm_username', ''), 'text');
        SiteSetting::set('netgsm_password', $request->input('netgsm_password', ''), 'text');
        SiteSetting::set('netgsm_msgheader', $request->input('netgsm_msgheader', ''), 'text');

        $typesConfig = SmsNotificationService::getTypesConfig();
        $types = [];
        foreach (array_keys($typesConfig) as $key) {
            $types[$key] = $request->boolean('sms_type_' . $key);
        }
        SiteSetting::set('netgsm_sms_types', $types, 'json');

        return redirect()->route('superadmin.netgsm-settings.index')
            ->with('success', 'NetGSM ve SMS bildirim ayarları güncellendi.');
    }

    /**
     * Test SMS gönder (superadmin panelinden).
     */
    public function testSend(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
            'test_message' => 'nullable|string|max:320',
        ]);

        $phone = $request->input('test_phone');
        $message = $request->input('test_message', 'Spordosyam test SMS (deneme).');

        $netgsm = app(NetGsmService::class);
        if (!$netgsm->isConfigured()) {
            $testInfo = [
                'configured' => false,
                'username' => SiteSetting::get('netgsm_username', ''),
                'msgheader' => SiteSetting::get('netgsm_msgheader', ''),
                'normalized_phone' => $this->normalizePhoneForDisplay($phone),
                'result' => ['success' => false, 'error' => 'NetGSM ayarları tamamlanmamış (username/password/msgheader).'],
            ];
            return redirect()->back()->with('netgsm_test', $testInfo)->with('error', 'NetGSM ayarları tamamlanmamış (username/password/msgheader).');
        }

        $result = $netgsm->send($phone, $message);
        // Build detailed test info for UI
        $testInfo = [
            'configured' => true,
            'username' => SiteSetting::get('netgsm_username', ''),
            'msgheader' => SiteSetting::get('netgsm_msgheader', ''),
            'normalized_phone' => $this->normalizePhoneForDisplay($phone),
            'result' => $result,
        ];

        // remove previous global success/error flashes so test area shows only test-specific info
        session()->forget('success');
        session()->forget('error');

        if (!empty($result['success'])) {
            return redirect()->back()->with('netgsm_test', $testInfo);
        }

        $err = $result['error'] ?? ($result['code'] ?? 'Bilinmeyen hata');
        Log::warning('NetGSM test-send failed', ['phone' => $phone, 'result' => $result]);
        return redirect()->back()->with('netgsm_test', $testInfo);
    }

    private function normalizePhoneForDisplay(string $phone): string
    {
        $p = preg_replace('/\D/', '', $phone);
        if (str_starts_with($p, '90') && strlen($p) === 12) {
            return $p;
        }
        if (str_starts_with($p, '0') && strlen($p) === 11) {
            return '9' . $p;
        }
        if (strlen($p) === 10 && str_starts_with($p, '5')) {
            return '90' . $p;
        }
        return $p;
    }
}
