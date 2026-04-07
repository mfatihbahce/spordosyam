<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LicenseExpiredController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $school = $user ? $user->getSchoolForLicense() : null;
        $licenseType = $school ? $school->license_type : null;

        $messages = [
            'demo' => 'Demo süreniz sona erdi. Devam etmek için lisans satın almanız gerekmektedir.',
            'free' => 'Ücretsiz lisans süreniz sona erdi. Devam etmek için ücretli lisans satın almanız gerekmektedir.',
            'paid' => 'Lisans süreniz sona erdi. Devam etmek için lisans yenilemeniz gerekmektedir.',
        ];
        $message = $messages[$licenseType] ?? 'Lisans süreniz sona erdi. Devam etmek için lisans satın almanız gerekmektedir.';

        return view('license-expired', compact('message', 'licenseType'));
    }
}
