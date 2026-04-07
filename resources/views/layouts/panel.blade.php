@php
    $panelDesign = \App\Models\SiteSetting::get('active_design', 'design_1');
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') - Spordosyam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.06); }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 3px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.35); }
        .content-scroll::-webkit-scrollbar { width: 8px; }
        .content-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .content-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        .content-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        /* Tasarım 2: koyu scrollbar */
        [data-panel-design="design_2"] .content-scroll::-webkit-scrollbar-track { background: rgb(24 24 27); }
        [data-panel-design="design_2"] .content-scroll::-webkit-scrollbar-thumb { background: rgb(63 63 70); }
        [data-panel-design="design_2"] .content-scroll::-webkit-scrollbar-thumb:hover { background: rgb(82 82 91); }
        /* Tasarım 2: içerik alanı varsayılan koyu stiller (kart, metin, tablo) */
        [data-panel-design="design_2"] .panel-d2-content .bg-white { background: rgb(39 39 42); }
        [data-panel-design="design_2"] .panel-d2-content .bg-gray-50 { background: rgb(24 24 27); }
        [data-panel-design="design_2"] .panel-d2-content .bg-slate-50 { background: rgb(24 24 27); }
        [data-panel-design="design_2"] .panel-d2-content .text-gray-900 { color: rgb(244 244 245); }
        [data-panel-design="design_2"] .panel-d2-content .text-gray-800 { color: rgb(244 244 245); }
        [data-panel-design="design_2"] .panel-d2-content .text-gray-700 { color: rgb(228 228 231); }
        [data-panel-design="design_2"] .panel-d2-content .text-gray-600 { color: rgb(161 161 170); }
        [data-panel-design="design_2"] .panel-d2-content .text-gray-500 { color: rgb(113 113 122); }
        [data-panel-design="design_2"] .panel-d2-content .text-slate-800 { color: rgb(244 244 245); }
        [data-panel-design="design_2"] .panel-d2-content .text-slate-700 { color: rgb(228 228 231); }
        [data-panel-design="design_2"] .panel-d2-content .text-slate-500 { color: rgb(113 113 122); }
        [data-panel-design="design_2"] .panel-d2-content .border-gray-200 { border-color: rgb(63 63 70); }
        [data-panel-design="design_2"] .panel-d2-content .border-slate-200 { border-color: rgb(63 63 70); }
        [data-panel-design="design_2"] .panel-d2-content .border-gray-100 { border-color: rgb(39 39 42); }
        [data-panel-design="design_2"] .panel-d2-content .divide-gray-200 > * { border-color: rgb(63 63 70); }
        [data-panel-design="design_2"] .panel-d2-content input:not([type=checkbox]), [data-panel-design="design_2"] .panel-d2-content select, [data-panel-design="design_2"] .panel-d2-content textarea {
            background: rgb(39 39 42); border-color: rgb(63 63 70); color: rgb(244 244 245);
        }
        [data-panel-design="design_2"] .panel-d2-content thead.bg-gray-50 { background: rgb(39 39 42); }
        [data-panel-design="design_2"] .panel-d2-content tbody.bg-white { background: rgb(24 24 27); }
        [data-panel-design="design_2"] .panel-d2-content .bg-indigo-600 { background: rgb(16 185 129); }
        [data-panel-design="design_2"] .panel-d2-content .hover\:bg-indigo-700:hover { background: rgb(5 150 105); }
        [data-panel-design="design_2"] .panel-d2-content .bg-green-50 { background: rgba(6 95 70, 0.3); }
        [data-panel-design="design_2"] .panel-d2-content .bg-red-50 { background: rgba(127 29 29, 0.3); }
        [data-panel-design="design_2"] .panel-d2-content .bg-blue-50 { background: rgba(30 58 138, 0.3); }
        [data-panel-design="design_2"] .panel-d2-content .bg-yellow-50, [data-panel-design="design_2"] .panel-d2-content .bg-amber-50 { background: rgba(120 53 15, 0.3); }
        /* Tasarım 2: sidebar menü linkleri */
        [data-panel-design="design_2"] aside a { color: rgb(161 161 170); }
        [data-panel-design="design_2"] aside a:hover { color: rgb(244 244 245); background: rgb(39 39 42); }
        [data-panel-design="design_2"] aside .bg-indigo-600,
        [data-panel-design="design_2"] aside .bg-orange-500 { background: rgb(16 185 129); color: rgb(24 24 27); }
        [data-panel-design="design_2"] aside .border-l-4 { border-color: rgb(16 185 129); }
    </style>
    @stack('styles')
</head>
<body class="h-screen overflow-hidden {{ $panelDesign === 'design_2' ? 'bg-zinc-950 text-zinc-300' : 'bg-gray-50' }}" data-panel-design="{{ $panelDesign }}">
    @include('layouts.panel-themes.' . $panelDesign)
    <script>
        (function() {
            var serverTimestampMs = {{ now()->timestamp * 1000 }};
            var pageLoadMs = Date.now();
            var trZone = 'Europe/Istanbul';
            function updateClock() {
                var d = new Date(serverTimestampMs + (Date.now() - pageLoadMs));
                var el = document.getElementById('panel-clock-time');
                var elDate = document.getElementById('panel-clock-date');
                if (el) el.textContent = d.toLocaleTimeString('tr-TR', { timeZone: trZone, hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                if (elDate) elDate.textContent = d.toLocaleDateString('tr-TR', { timeZone: trZone, day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '.');
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
