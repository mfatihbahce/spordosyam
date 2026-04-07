<div class="px-3 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('parent.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.dashboard') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span class="font-medium">Dashboard</span>
    </a>
    
    <!-- Çocuğum -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Öğrenci</p>
        <a href="{{ route('parent.student.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.student.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-graduate w-5 mr-3"></i>
            <span>Çocuğum</span>
        </a>
    </div>
    
    <!-- Devam Takibi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Takip</p>
        <a href="{{ route('parent.attendances.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.attendances.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-check-circle w-5 mr-3"></i>
            <span>Yoklamalar</span>
        </a>
        <a href="{{ route('parent.progress.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.progress.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-chart-line w-5 mr-3"></i>
            <span>Gelişim Notları</span>
        </a>
    </div>

    @php
        $parentUser = \App\Models\ParentModel::where('user_id', Auth::id())->first();
        $parentSchool = $parentUser?->school;
    @endphp
    @if($parentSchool && $parentSchool->makeup_class_enabled)
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Telafi</p>
        <a href="{{ route('parent.makeup-sessions.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.makeup-sessions.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-plus w-5 mr-3"></i>
            <span>Telafi Dersleri</span>
        </a>
    </div>
    @endif
    
    <!-- İçerikler -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">İçerik</p>
        <a href="{{ route('parent.media.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.media.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-images w-5 mr-3"></i>
            <span>Paylaşımlar</span>
        </a>
    </div>
    
    <!-- Ödemeler -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ödemeler</p>
        <a href="{{ route('parent.payments.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.payments.index') || request()->routeIs('parent.payments.create') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-money-bill-wave w-5 mr-3"></i>
            <span>Aidatlarım</span>
        </a>
        <a href="{{ route('parent.payments.history') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.payments.history') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-history w-5 mr-3"></i>
            <span>Ödeme Geçmişi</span>
        </a>
        <a href="{{ route('parent.invoices.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.invoices.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-file-invoice w-5 mr-3"></i>
            <span>Faturalar</span>
        </a>
    </div>
    
    <!-- İletişim -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">İletişim</p>
        <a href="{{ route('parent.messages.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.messages.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-comments w-5 mr-3"></i>
            <span>Mesajlar</span>
        </a>
    </div>
    
    <!-- Ayarlar -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Hesap</p>
        <a href="{{ route('parent.profile.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('parent.profile.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user w-5 mr-3"></i>
            <span>Profil</span>
        </a>
    </div>
</div>
