<div class="px-3 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('coach.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.dashboard') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span class="font-medium">Dashboard</span>
    </a>
    
    <!-- Sınıflarım -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sınıflar</p>
        <a href="{{ route('coach.classes.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.classes.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-book w-5 mr-3"></i>
            <span>Sınıflar</span>
        </a>
    </div>
    
    <!-- Yoklama -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Yoklama</p>
        <a href="{{ route('coach.attendances.create') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.attendances.create') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-check-square w-5 mr-3"></i>
            <span>Yoklama Al</span>
        </a>
        <a href="{{ route('coach.attendances.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.attendances.index') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-history w-5 mr-3"></i>
            <span>Yoklama Geçmişi</span>
        </a>
    </div>

    @php
        $coach = \App\Models\Coach::where('user_id', Auth::id())->first();
        $coachSchool = $coach?->school;
    @endphp
    @if($coachSchool && $coachSchool->makeup_class_enabled)
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Telafi</p>
        <a href="{{ route('coach.makeup-sessions.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.makeup-sessions.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-plus w-5 mr-3"></i>
            <span>Telafi Derslerim</span>
        </a>
    </div>
    @endif
    
    <!-- Öğrenci Gelişimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gelişim</p>
        <a href="{{ route('coach.student-progress.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.student-progress.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-chart-line w-5 mr-3"></i>
            <span>Gelişim Notları</span>
        </a>
    </div>
    
    <!-- İçerik Paylaşımı -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Paylaşım</p>
        <a href="{{ route('coach.media.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.media.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-images w-5 mr-3"></i>
            <span>Paylaşımlarım</span>
        </a>
    </div>
    
    <!-- Raporlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Raporlar</p>
        <a href="{{ route('coach.reports.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.reports.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-file-alt w-5 mr-3"></i>
            <span>Raporlarım</span>
        </a>
    </div>

    <!-- Mesajlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">İletişim</p>
        <a href="{{ route('coach.messages.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.messages.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-comments w-5 mr-3"></i>
            <span>Mesajlar</span>
        </a>
    </div>
    
    <!-- Ayarlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Hesap</p>
        <a href="{{ route('coach.profile.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('coach.profile.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user w-5 mr-3"></i>
            <span>Profil</span>
        </a>
    </div>
</div>
