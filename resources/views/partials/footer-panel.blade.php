@php
    use App\Models\SiteSetting;
    $footerCopyright = SiteSetting::get('footer_copyright', 'Spordosyam. Tüm hakları saklıdır.');
@endphp
<footer class="bg-gray-900 border-t border-gray-800 py-4">
    <p class="text-center text-gray-400 text-xs">
        &copy; {{ date('Y') }} {{ $footerCopyright }}
    </p>
</footer>
