<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function help()
    {
        return view('pages.help');
    }

    public function contact()
    {
        $footerEmail = SiteSetting::get('footer_email', '');
        $footerPhone = SiteSetting::get('footer_phone', '');
        $footerAddress = SiteSetting::get('footer_address', '');
        
        return view('pages.contact', compact('footerEmail', 'footerPhone', 'footerAddress'));
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
