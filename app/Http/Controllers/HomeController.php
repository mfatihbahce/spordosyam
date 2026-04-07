<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $theme = SiteSetting::get('homepage_theme', 'theme_1');
        return view('home-themes.' . $theme);
    }
}
