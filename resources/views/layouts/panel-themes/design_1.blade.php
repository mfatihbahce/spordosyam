{{-- Tasarım 1: Mevcut açık tema --}}
<div class="flex h-full">
    <aside class="w-64 bg-gray-900 text-white flex flex-col shadow-2xl flex-shrink-0">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dumbbell text-indigo-400 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">Spordosyam</h1>
                    @php
                        $panelLabels = [
                            'parent' => 'Veli Paneli',
                            'coach' => 'Antrenör Paneli',
                            'admin' => 'Yönetici Paneli',
                            'superadmin' => 'Süper Yönetici Paneli',
                        ];
                        $panelTitle = $panelLabels[auth()->user()->role] ?? (ucfirst(auth()->user()->role) . ' Paneli');
                    @endphp
                    <p class="text-xs text-gray-400">{{ $panelTitle }}</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
            @yield('sidebar-menu')
        </nav>
        <div class="p-4 border-t border-gray-800 bg-gray-800/50">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-300"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'Hoş geldiniz')</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="panel-clock" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-gray-700 text-sm tabular-nums" title="Türkiye saati ({{ config('app.timezone') }})">
                        <i class="fas fa-clock text-indigo-500"></i>
                        <span id="panel-clock-time">--:--:--</span>
                        <span id="panel-clock-date" class="text-gray-500 text-xs">--.--.----</span>
                    </div>
                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </header>

        @php
            $licenseSchool = auth()->user()->getSchoolForLicense();
            $licenseDaysLeft = $licenseSchool ? $licenseSchool->getDaysUntilLicenseExpires() : null;
            $showLicenseWarning = $licenseSchool && $licenseDaysLeft !== null && $licenseDaysLeft >= 0 && $licenseDaysLeft <= 10 && !auth()->user()->isSuperAdmin();
        @endphp
        @if($showLicenseWarning)
        <div class="bg-amber-50 border-b border-amber-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <p class="text-sm text-amber-800">
                <i class="fas fa-exclamation-triangle mr-2 text-amber-600"></i>
                @if($licenseDaysLeft === 0)
                    Lisansınız bugün sona eriyor. Lütfen lisans yenilemesi yapın.
                @else
                    Lisansınız {{ $licenseDaysLeft }} gün sonra sona erecek. Lütfen lisans yenilemesi yapın.
                @endif
            </p>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-amber-700 hover:text-amber-900 underline">İletişime Geç</a>
        </div>
        @endif

        <main class="flex-1 overflow-y-auto content-scroll bg-gray-50">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-check-circle mr-2"></i><p>{{ session('success') }}</p></div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i><p>{{ session('error') }}</p></div>
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-info-circle mr-2"></i><p>{{ session('info') }}</p></div>
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i><p>{{ session('warning') }}</p></div>
                </div>
            @endif
            <div class="p-6">
                @yield('content')
            </div>
        </main>
        <div class="flex-shrink-0">
            @include('partials.footer-panel')
        </div>
    </div>
</div>
