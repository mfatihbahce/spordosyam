@php
    use App\Models\SiteSetting;
    $footerCopyright = SiteSetting::get('footer_copyright', 'Spordosyam. Tüm hakları saklıdır.');
@endphp
<footer class="py-4">
    <p class="text-center text-zinc-500 text-xs">
        &copy; {{ date('Y') }} {{ $footerCopyright }}
    </p>
</footer>
