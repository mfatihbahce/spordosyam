@extends('layouts.panel')

@section('title', 'NetGSM / SMS Ayarları')
@section('page-title', 'NetGSM / SMS Ayarları')
@section('page-description', 'SMS bildirimleri (NetGSM) ve hangi olaylarda kime SMS gideceği')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="w-full max-w-none">
    @if(session('success') && !session('netgsm_test'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('superadmin.netgsm-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            {{-- NetGSM Kimlik Bilgileri --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">NetGSM Kimlik Bilgileri</h2>
                <p class="text-sm text-gray-500 mb-4">SMS gönderimi için NetGSM panelinden alacağınız bilgileri girin. Başlık (msgheader) IYS onaylı olmalıdır.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kullanıcı Adı (usercode)</label>
                        <input type="text" name="netgsm_username" value="{{ old('netgsm_username', $credentials['netgsm_username']) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="NetGSM kullanıcı adı">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Şifre</label>
                        <input type="password" name="netgsm_password" value="{{ old('netgsm_password', $credentials['netgsm_password']) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="NetGSM şifre">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj Başlığı (max 11 karakter)</label>
                        <input type="text" name="netgsm_msgheader" value="{{ old('netgsm_msgheader', $credentials['netgsm_msgheader']) }}" maxlength="11"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="SPORDOSYAM">
                    </div>
                </div>
            </div>
            {{-- NetGSM Kimlik bilgileri içindeki Test butonu (sabit numara) - form dışında, form="netgsm-test-form" ile bağlı --}}
            <div class="mt-4 flex items-center gap-3">
                <button type="submit" form="netgsm-test-form" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                    <i class="fas fa-bolt"></i> Bağlantıyı Test Et (5333178197)
                </button>
                <span class="text-sm text-gray-500">Bu buton her zaman 5333178197 numarasına test SMS gönderir ve sonucu aşağıda gösterir.</span>
            </div>
            @if(session('netgsm_test'))
            @php $t = session('netgsm_test'); @endphp
            <div class="bg-white rounded-lg shadow p-4 border border-gray-100 mt-4">
                <h3 class="text-sm font-semibold mb-2">Test Sonucu</h3>
                <div class="text-sm text-gray-700 space-y-2">
                    <div><strong>Configured:</strong> {{ $t['configured'] ? 'Evet' : 'Hayır' }}</div>
                    <div><strong>Username:</strong> {{ $t['username'] ?? '-' }}</div>
                    <div><strong>MsgHeader:</strong> {{ $t['msgheader'] ?? '-' }}</div>
                    <div><strong>Normalized phone:</strong> {{ $t['normalized_phone'] ?? '-' }}</div>
                    <div><strong>Result:</strong></div>
                    <pre class="bg-gray-50 p-3 rounded text-xs text-gray-600 overflow-auto">{{ json_encode($t['result'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            {{-- Veliye Giden SMS Türleri --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Velilere Giden SMS Türleri</h2>
                <p class="text-sm text-gray-500 mb-4">Hangi olaylarda veliye SMS gönderilsin? Açık olanlar için ilgili veli telefon numarasına SMS gider.</p>
                <ul class="space-y-3">
                    @foreach($veliTypes as $key => $config)
                    <li class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <label for="sms_type_{{ $key }}" class="text-sm text-gray-800 cursor-pointer flex-1">{{ $config['label'] }}</label>
                        <input type="hidden" name="sms_type_{{ $key }}" value="0">
                        <input type="checkbox" name="sms_type_{{ $key }}" id="sms_type_{{ $key }}" value="1"
                               {{ old('sms_type_'.$key, $enabledTypes[$key] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Antrenöre Giden SMS Türleri --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Antrenörlere Giden SMS Türleri</h2>
                <p class="text-sm text-gray-500 mb-4">Hangi olaylarda antrenöre SMS gönderilsin? Açık olanlar için ilgili antrenör telefon numarasına SMS gider.</p>
                <ul class="space-y-3">
                    @foreach($antrenorTypes as $key => $config)
                    <li class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <label for="sms_type_{{ $key }}" class="text-sm text-gray-800 cursor-pointer flex-1">{{ $config['label'] }}</label>
                        <input type="hidden" name="sms_type_{{ $key }}" value="0">
                        <input type="checkbox" name="sms_type_{{ $key }}" id="sms_type_{{ $key }}" value="1"
                               {{ old('sms_type_'.$key, $enabledTypes[$key] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm">
                <i class="fas fa-save mr-2"></i> Kaydet
            </button>
        </div>
    </form>

    {{-- Test formu ana formun dışında; buton form="netgsm-test-form" ile buna bağlı --}}
    <form id="netgsm-test-form" action="{{ route('superadmin.netgsm-settings.test-send') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="test_phone" value="5333178197">
        <input type="hidden" name="test_message" value="SPORDOSYAM test SMS">
    </form>
</div>
@endsection
