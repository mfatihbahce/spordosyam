@extends('layouts.app')

@section('title', 'Şifre Sıfırla')

@section('content')
@php $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2'; @endphp
<div class="min-h-screen {{ $isT2 ? 'bg-slate-50' : 'bg-gray-50' }}">
    @include('partials.header')

    <div class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 {{ $isT2 ? 'bg-gradient-to-br from-emerald-500 to-teal-600' : 'bg-indigo-600' }} rounded-xl mb-6 shadow-md">
                    <i class="fas fa-lock-open text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Spordosyam</h1>
                <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-500' }} text-sm">Yeni şifre belirleyin</p>
            </div>

            <div class="bg-white {{ $isT2 ? 'rounded-2xl shadow-sm border border-slate-200' : 'rounded-lg shadow-sm border border-gray-200' }}">
                <div class="px-8 py-6 border-b {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }}">
                    <h2 class="text-xl font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Şifre Sıfırla</h2>
                </div>

                <form class="p-8 space-y-5" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            E-posta Adresi
                        </label>
                        <input id="email" name="email" type="email" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors"
                               placeholder="ornek@spordosyam.com" value="{{ old('email', $email) }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Yeni Şifre
                        </label>
                        <input id="password" name="password" type="password" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors"
                               placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Yeni Şifre (Tekrar)
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors"
                               placeholder="••••••••">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex items-center justify-center py-3 px-4 border border-transparent {{ $isT2 ? 'rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:ring-emerald-500' : 'rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' }} text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                            Şifremi Sıfırla
                        </button>
                    </div>

                    <div class="text-center pt-4 border-t {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }}">
                        <a href="{{ route('login') }}" class="text-sm font-medium {{ $isT2 ? 'text-emerald-600 hover:text-emerald-700' : 'text-indigo-600 hover:text-indigo-500' }}">
                            ← Giriş sayfasına dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
