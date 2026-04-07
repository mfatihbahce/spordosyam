@extends('layouts.panel')

@section('title', 'Footer Ayarları')
@section('page-title', 'Footer Ayarları')
@section('page-description', 'Anasayfa footer içeriğini düzenleyin')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="w-full max-w-none">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('superadmin.footer-settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Footer Açıklama -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Footer Açıklama</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama Metni</label>
                    <textarea name="footer_description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('footer_description', $settings['footer_description']) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Footer'da görünecek açıklama metni</p>
                </div>
            </div>

            <!-- Sosyal Medya Linkleri -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sosyal Medya Linkleri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                        <input type="url" name="footer_facebook_url" value="{{ old('footer_facebook_url', $settings['footer_facebook_url']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="https://facebook.com/spordosyam">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                        <input type="url" name="footer_twitter_url" value="{{ old('footer_twitter_url', $settings['footer_twitter_url']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="https://twitter.com/spordosyam">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                        <input type="url" name="footer_instagram_url" value="{{ old('footer_instagram_url', $settings['footer_instagram_url']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="https://instagram.com/spordosyam">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                        <input type="url" name="footer_linkedin_url" value="{{ old('footer_linkedin_url', $settings['footer_linkedin_url']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="https://linkedin.com/company/spordosyam">
                    </div>
                </div>
            </div>

            <!-- Hızlı Linkler -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı Linkler</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                    <input type="text" name="footer_quick_links_title" value="{{ old('footer_quick_links_title', $settings['footer_quick_links_title']) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div id="quick-links-container">
                    @foreach(old('footer_quick_links', $settings['footer_quick_links'] ?? []) as $index => $link)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 quick-link-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                            <input type="text" name="footer_quick_links[{{ $index }}][title]" value="{{ $link['title'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                            <div class="flex gap-2">
                                <input type="text" name="footer_quick_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                                <button type="button" onclick="removeQuickLink(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addQuickLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Link Ekle
                </button>
            </div>

            <!-- Hakkımızda -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hakkımızda</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                    <input type="text" name="footer_about_title" value="{{ old('footer_about_title', $settings['footer_about_title']) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                    <textarea name="footer_about_text" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('footer_about_text', $settings['footer_about_text']) }}</textarea>
                </div>
            </div>

            <!-- İletişim Bilgileri -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">İletişim Bilgileri</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                    <input type="text" name="footer_contact_title" value="{{ old('footer_contact_title', $settings['footer_contact_title']) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                        <input type="email" name="footer_email" value="{{ old('footer_email', $settings['footer_email']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="info@spordosyam.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                        <input type="text" name="footer_phone" value="{{ old('footer_phone', $settings['footer_phone']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="+90 (212) 555 00 00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
                        <input type="text" name="footer_address" value="{{ old('footer_address', $settings['footer_address']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="İstanbul, Türkiye">
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Copyright</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Metni</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="Spordosyam. Tüm hakları saklıdır.">
                    <p class="text-xs text-gray-500 mt-1">{{ date('Y') }} yılı otomatik olarak eklenecektir.</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i>Kaydet
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let quickLinkIndex = {{ count($settings['footer_quick_links'] ?? []) }};

function addQuickLink() {
    const container = document.getElementById('quick-links-container');
    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 quick-link-item">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlık</label>
                <input type="text" name="footer_quick_links[${quickLinkIndex}][title]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                <div class="flex gap-2">
                    <input type="text" name="footer_quick_links[${quickLinkIndex}][url]" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    <button type="button" onclick="removeQuickLink(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    quickLinkIndex++;
}

function removeQuickLink(button) {
    button.closest('.quick-link-item').remove();
}
</script>
@endpush
@endsection
