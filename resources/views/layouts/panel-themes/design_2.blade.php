{{-- Tasarım 2: Koyu tema, minimal, kullanıcı dostu --}}
<div class="flex h-full">
    <aside class="w-64 bg-zinc-900 border-r border-zinc-800 flex flex-col flex-shrink-0">
        <div class="p-5 border-b border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dumbbell text-emerald-400 text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-zinc-100 truncate">Spordosyam</h1>
                    @php
                        $panelLabels = [
                            'parent' => 'Veli Paneli',
                            'coach' => 'Antrenör Paneli',
                            'admin' => 'Yönetici Paneli',
                            'superadmin' => 'Süper Yönetici Paneli',
                        ];
                        $panelTitle = $panelLabels[auth()->user()->role] ?? (ucfirst(auth()->user()->role) . ' Paneli');
                    @endphp
                    <p class="text-xs text-zinc-500">{{ $panelTitle }}</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-3 px-2">
            @yield('sidebar-menu')
        </nav>
        <div class="p-4 border-t border-zinc-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-zinc-800 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-zinc-400 text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-200 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-zinc-100 rounded-lg transition-colors text-sm">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0 bg-zinc-950">
        <header class="bg-zinc-900/80 border-b border-zinc-800 flex-shrink-0 backdrop-blur-sm">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-100">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-zinc-500 mt-0.5">@yield('page-description', 'Hoş geldiniz')</p>
                </div>
                <div class="flex items-center gap-3">
                    <div id="panel-clock" class="flex items-center gap-2 px-3 py-2 bg-zinc-800/80 rounded-lg text-zinc-300 text-sm tabular-nums" title="Türkiye saati ({{ config('app.timezone') }})">
                        <i class="fas fa-clock text-emerald-500 text-xs"></i>
                        <span id="panel-clock-time">--:--:--</span>
                        <span id="panel-clock-date" class="text-zinc-500 text-xs">--.--.----</span>
                    </div>
                    <button class="p-2 text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800 rounded-lg transition-colors">
                        <i class="fas fa-bell text-sm"></i>
                    </button>
                    <button class="p-2 text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800 rounded-lg transition-colors">
                        <i class="fas fa-cog text-sm"></i>
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
        <div class="bg-amber-950/50 border-b border-amber-800/50 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <p class="text-sm text-amber-200">
                <i class="fas fa-exclamation-triangle mr-2 text-amber-500"></i>
                @if($licenseDaysLeft === 0)
                    Lisansınız bugün sona eriyor. Lütfen lisans yenilemesi yapın.
                @else
                    Lisansınız {{ $licenseDaysLeft }} gün sonra sona erecek. Lütfen lisans yenilemesi yapın.
                @endif
            </p>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-amber-400 hover:text-amber-300 underline">İletişime Geç</a>
        </div>
        @endif

        <main class="flex-1 overflow-y-auto content-scroll bg-zinc-950 text-zinc-300">
            @if(session('success'))
                <div class="mb-4 bg-emerald-950/40 border-l-4 border-emerald-500 text-emerald-200 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-check-circle mr-2 text-emerald-400"></i><p>{{ session('success') }}</p></div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-950/40 border-l-4 border-red-500 text-red-200 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-exclamation-circle mr-2 text-red-400"></i><p>{{ session('error') }}</p></div>
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-sky-950/40 border-l-4 border-sky-500 text-sky-200 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-info-circle mr-2 text-sky-400"></i><p>{{ session('info') }}</p></div>
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 bg-amber-950/40 border-l-4 border-amber-500 text-amber-200 p-4 rounded-r-lg">
                    <div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2 text-amber-400"></i><p>{{ session('warning') }}</p></div>
                </div>
            @endif
            <div class="p-6 panel-d2-content">
                @yield('content')
            </div>
        </main>
        <div class="flex-shrink-0 border-t border-zinc-800 bg-zinc-900/50">
            @include('partials.footer-panel-design2')
        </div>
    </div>
</div>
