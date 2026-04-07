@php $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2'; @endphp
<nav class="{{ $isT2 ? 'bg-white/95 border-b border-slate-200/80 backdrop-blur-md shadow-sm' : 'bg-white shadow-sm' }} sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center {{ $isT2 ? 'gap-2' : 'space-x-2' }}">
                <div class="{{ $isT2 ? 'w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-md' : 'w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg' }} flex items-center justify-center">
                    <span class="text-white font-bold {{ $isT2 ? 'text-lg' : 'text-xl' }}">S</span>
                </div>
                <a href="{{ route('home') }}" class="{{ $isT2 ? 'text-xl font-semibold text-slate-800' : 'text-2xl font-bold text-gray-900' }}">Spordosyam</a>
            </div>
            <div class="hidden md:flex items-center {{ $isT2 ? 'gap-8' : 'space-x-4' }}">
                <a href="{{ route('home') }}#ozellikler" class="text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600' }} transition-colors">Özellikler</a>
                <a href="{{ route('home') }}#nasil-calisir" class="text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600' }} transition-colors">Nasıl Çalışır</a>
                <a href="{{ route('faq') }}" class="text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600' }} transition-colors">SSS</a>
                <a href="{{ route('help') }}" class="text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600' }} transition-colors">Yardım</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600' }} transition-colors">İletişim</a>
            </div>
            <div class="flex items-center {{ $isT2 ? 'gap-3' : 'space-x-4' }}">
                @auth
                    @php
                        $dashboardRoute = match(auth()->user()->role) {
                            'superadmin' => route('superadmin.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'coach' => route('coach.dashboard'),
                            'parent' => route('parent.dashboard'),
                            default => route('home'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="{{ $isT2 ? 'px-4 py-2 text-sm font-medium text-slate-600 border border-slate-300 rounded-xl hover:border-emerald-400 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-gray-200 hover:border-indigo-300' }} transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>Panel
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="{{ $isT2 ? 'px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 shadow-md' : 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 px-6 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg' }} transition-all">
                            <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ $isT2 ? 'text-sm font-medium text-slate-600 hover:text-emerald-600' : 'text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-lg text-sm font-medium' }} transition-colors">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="{{ $isT2 ? 'px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 shadow-md' : 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 px-6 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg' }} transition-all">Demo Talep Et</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
