@extends('layouts.app')

@section('title', 'Lisans Süresi Doldu')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-200 p-8 text-center">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation-triangle text-amber-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Lisans Süresi Doldu</h1>
        <p class="text-gray-600 mb-6">{{ $message }}</p>
        <p class="text-sm text-gray-500 mb-6">
            Lisans satın almak veya yenilemek için Spordosyam ile iletişime geçin. Okul yöneticiniz (admin) üzerinden de talepte bulunabilirsiniz.
        </p>
        <div class="space-y-3">
            <a href="{{ route('contact') }}" class="block w-full py-3 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                İletişime Geç
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-full py-3 px-4 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Çıkış Yap
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
