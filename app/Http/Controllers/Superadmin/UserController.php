<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('school')->orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('school', 'coach', 'parent');
        return view('superadmin.users.show', compact('user'));
    }
}
