@extends('layouts.panel')

@section('title', 'IBAN Yönetimi')
@section('page-title', 'IBAN Yönetimi')
@section('page-description', 'Banka hesaplarınızı yönetin')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Bilgilendirme Mesajları -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Iyzico Durum Bilgilendirmesi -->
    @if(Auth::user()->school->iyzico_sub_merchant_key)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Iyzico Entegrasyonu Aktif:</strong> Ödemeler otomatik olarak Iyzico üzerinden işlenecek ve hesabınıza yatırılacaktır.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Iyzico Entegrasyonu Henüz Aktif Değil:</strong> Ödemeleri alabilmek için "Yeni Hesap Ekle" ile banka hesabı ekleyin. Hesap kaydedildiğinde Iyzico üye alt işyeri (sub-merchant) otomatik oluşturulur.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Banka Hesapları</h3>
            <p class="text-sm text-gray-500 mt-1">
                @if($bankAccounts->isEmpty())
                    Banka hesabı eklediğinizde Iyzico üye alt işyeri otomatik oluşturulur
                @else
                    Mevcut hesabınızı düzenleyebilirsiniz. Yalnızca bir banka hesabı tanımlanabilir.
                @endif
            </p>
        </div>
        @if($bankAccounts->isEmpty())
            <a href="{{ route('admin.bank-accounts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>Yeni Hesap Ekle
            </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hesap Sahibi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IBAN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Banka</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alt İşyeri Tipi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bankAccounts as $account)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_holder_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($account->iban)
                                {{ substr($account->iban, 0, 8) }}****{{ substr($account->iban, -4) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->bank_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($account->sub_merchant_type)
                                @if($account->sub_merchant_type === 'PERSONAL')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Bireysel</span>
                                @elseif($account->sub_merchant_type === 'PRIVATE_COMPANY')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Şahıs Şirketi</span>
                                @elseif($account->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Limited/Anonim</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                @if($account->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                                @endif
                                @if($account->is_verified || Auth::user()->school->iyzico_sub_merchant_key)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 block mt-1">Iyzico Aktif</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(!$account->is_verified && !Auth::user()->school->iyzico_sub_merchant_key && $account->sub_merchant_type && $account->is_active)
                                    <form action="{{ route('admin.bank-accounts.create-submerchant', $account) }}" method="POST" class="inline" onsubmit="return confirm('Iyzico alt işyeri (sub-merchant) oluşturulacak. Devam etmek istediğinize emin misiniz?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700" title="Iyzico Alt İşyeri Oluştur">
                                            <i class="fas fa-plus-circle mr-1"></i> Sub-Merchant Oluştur
                                        </button>
                                    </form>
                                @elseif(!$account->is_verified && !Auth::user()->school->iyzico_sub_merchant_key)
                                    <span class="text-xs text-gray-500" title="Sub-merchant oluşturmak için hesabı düzenleyip alt işyeri tipini seçin ve hesabı aktif yapın">
                                        <i class="fas fa-info-circle"></i> Düzenle
                                    </span>
                                @endif
                                <a href="{{ route('admin.bank-accounts.edit', $account) }}" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                    <i class="fas fa-edit mr-1"></i> Düzenle
                                </a>
                                @if(!$account->is_verified || !Auth::user()->school->iyzico_sub_merchant_key)
                                    <form action="{{ route('admin.bank-accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Bu banka hesabını silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            <i class="fas fa-trash mr-1"></i> Sil
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400" title="Iyzico entegrasyonu aktif olduğu için bu hesap silinemez">
                                        <i class="fas fa-lock"></i> Kilitli
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            <div class="py-8">
                                <i class="fas fa-university text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">Henüz banka hesabı eklenmemiş.</p>
                                <a href="{{ route('admin.bank-accounts.create') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>İlk Banka Hesabını Ekle
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bankAccounts->links() }}
        </div>
    </div>
</div>
@endsection
