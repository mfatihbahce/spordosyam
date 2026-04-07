<div class="px-3 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span class="font-medium">Dashboard</span>
    </a>
    
    <!-- Şube Yönetimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Yönetim</p>
        <a href="{{ route('admin.branches.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.branches.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-building w-5 mr-3"></i>
            <span>Şubeler</span>
        </a>
        <a href="{{ route('admin.sport-branches.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.sport-branches.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-futbol w-5 mr-3"></i>
            <span>Branşlar</span>
        </a>
        <a href="{{ route('admin.classes.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.classes.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-book w-5 mr-3"></i>
            <span>Sınıflar</span>
        </a>
    </div>
    
    <!-- Kişi Yönetimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kişiler</p>
        <a href="{{ route('admin.students.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.students.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-graduate w-5 mr-3"></i>
            <span>Öğrenciler</span>
        </a>
        <a href="{{ route('admin.coaches.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.coaches.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-tie w-5 mr-3"></i>
            <span>Antrenörler</span>
        </a>
        <a href="{{ route('admin.parents.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.parents.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-users w-5 mr-3"></i>
            <span>Veliler</span>
        </a>
    </div>
    
    <!-- Takip & İçerik -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Takip</p>
        <a href="{{ route('admin.attendances.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.attendances.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-check-circle w-5 mr-3"></i>
            <span>Yoklamalar</span>
        </a>
        <a href="{{ route('admin.media.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.media.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-images w-5 mr-3"></i>
            <span>Paylaşımlar</span>
        </a>
    </div>
    
    @php
        $school = Auth::user()->school;
    @endphp
    @if($school && $school->makeup_class_enabled)
    <!-- Telafi Dersleri -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Telafi Dersleri</p>
        <a href="{{ route('admin.class-cancellations.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.class-cancellations.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-times w-5 mr-3"></i>
            <span>İptal Olan Dersler</span>
        </a>
        <a href="{{ route('admin.makeup-sessions.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.makeup-sessions.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-plus w-5 mr-3"></i>
            <span>Telafi Dersleri</span>
        </a>
        <a href="{{ route('admin.student-makeup-classes.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.student-makeup-classes.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-clock w-5 mr-3"></i>
            <span>Telafi Bekleyenler</span>
        </a>
    </div>
    @endif
    
    <!-- Finans -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Finans</p>
        <a href="{{ route('admin.student-fees.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.student-fees.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
            <span>Öğrenci Aidatları</span>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            <span>Ödemeler</span>
        </a>
        <a href="{{ route('admin.bank-accounts.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-university w-5 mr-3"></i>
            <span>IBAN Yönetimi</span>
        </a>
    </div>
    
    <!-- Raporlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Raporlar</p>
        <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-chart-pie w-5 mr-3"></i>
            <span>Raporlar</span>
        </a>
    </div>
    
    <!-- Ayarlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ayarlar</p>
        <a href="{{ route('admin.school-settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.school-settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-school w-5 mr-3"></i>
            <span>Okul Bilgileri</span>
        </a>
        <a href="{{ route('admin.user-settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.user-settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-cog w-5 mr-3"></i>
            <span>Kullanıcı Ayarları</span>
        </a>
    </div>
</div>
