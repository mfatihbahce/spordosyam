<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:applications,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'message' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'school_name.required' => 'Spor okulu adı gereklidir.',
            'school_name.max' => 'Spor okulu adı en fazla 255 karakter olabilir.',
            'contact_name.required' => 'İletişim kişisi gereklidir.',
            'contact_name.max' => 'İletişim kişisi en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'email.max' => 'E-posta en fazla 255 karakter olabilir.',
            'email.unique' => 'Bu e-posta adresi ile daha önce başvuru yapılmış.',
            'phone.required' => 'Telefon numarası gereklidir.',
            'phone.max' => 'Telefon en fazla 20 karakter olabilir.',
            'password.required' => 'Şifre belirlemeniz gerekiyor.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        Application::create([
            'school_name' => $request->school_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'message' => $request->message,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        return redirect()->route('register')
            ->with('success', 'Demo talebiniz başarıyla gönderildi. Hesabınız onaylandığında belirlediğiniz şifre ile giriş yapabilirsiniz.');
    }
}
