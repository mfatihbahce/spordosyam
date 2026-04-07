<div class="px-3 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <i class="fas fa-chart-line w-5 mr-3"></i>
        <span class="font-medium">Dashboard</span>
    </a>
    
    <!-- Spor Okulları Yönetimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Spor Okulları</p>
        <a href="{{ route('superadmin.schools.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.schools.index') || request()->routeIs('superadmin.schools.show') || request()->routeIs('superadmin.schools.edit') || request()->routeIs('superadmin.schools.create') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-school w-5 mr-3"></i>
            <span>Okullar</span>
        </a>
        <a href="{{ route('superadmin.schools.expired') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.schools.expired') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-clock w-5 mr-3"></i>
            <span>Lisansı Biten Okullar</span>
        </a>
        <a href="{{ route('superadmin.applications.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.applications.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-file-alt w-5 mr-3"></i>
            <span>Bekleyen Başvurular</span>
        </a>
    </div>
    
    <!-- Ödeme Yönetimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ödeme Yönetimi</p>
        <a href="{{ route('superadmin.payments.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.payments.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            <span>Ödemeler</span>
        </a>
        <a href="{{ route('superadmin.distributions.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.distributions.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-share-alt w-5 mr-3"></i>
            <span>Dağıtımlar</span>
        </a>
        <a href="{{ route('superadmin.commission.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.commission.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-percent w-5 mr-3"></i>
            <span>Komisyon Ayarları</span>
        </a>
    </div>
    
    <!-- Raporlar & Analitik -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Raporlar</p>
        <a href="{{ route('superadmin.reports.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.reports.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-clipboard-list w-5 mr-3"></i>
            <span>Genel Raporlar</span>
        </a>
        <a href="{{ route('superadmin.analytics.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.analytics.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-chart-bar w-5 mr-3"></i>
            <span>Grafikler</span>
        </a>
    </div>
    
    <!-- Kullanıcı Yönetimi -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sistem</p>
        <a href="{{ route('superadmin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.users.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-users w-5 mr-3"></i>
            <span>Kullanıcılar</span>
        </a>
    </div>
    
    <!-- Sistem Ayarları -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ayarlar</p>
        <a href="{{ route('superadmin.settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-cog w-5 mr-3"></i>
            <span>Genel Ayarlar</span>
        </a>
        <a href="{{ route('superadmin.payment-settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.payment-settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-credit-card w-5 mr-3"></i>
            <span>Ödeme Ayarları</span>
        </a>
        <a href="{{ route('superadmin.security.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.security.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-shield-alt w-5 mr-3"></i>
            <span>Güvenlik</span>
        </a>
        <a href="{{ route('superadmin.footer-settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.footer-settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-window-restore w-5 mr-3"></i>
            <span>Footer Ayarları</span>
        </a>
        <a href="{{ route('superadmin.netgsm-settings.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all {{ request()->routeIs('superadmin.netgsm-settings.*') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-sms w-5 mr-3"></i>
            <span>NetGSM / SMS</span>
        </a>
    </div>
</div>
