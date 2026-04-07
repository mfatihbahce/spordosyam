@extends('layouts.panel')

@section('title', 'Komisyon Ayarları')
@section('page-title', 'Komisyon Ayarları')
@section('page-description', 'Okulların komisyon oranlarını yönetin')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Okul Komisyon Oranları</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Okul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mevcut Komisyon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Dağıtım</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schools as $school)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $school->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($school->iyzico_commission_rate ?? 0, 2) }}%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($school->distributions->where('status', 'completed')->sum('net_amount'), 2) }} ₺
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="openModal({{ $school->id }}, '{{ $school->name }}', {{ $school->iyzico_commission_rate ?? 0 }})" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i> Düzenle
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Henüz okul bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $schools->links() }}
    </div>
</div>

<!-- Modal -->
<div id="commissionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Komisyon Oranı Düzenle</h3>
            <form id="commissionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Okul</label>
                    <p id="schoolName" class="text-sm text-gray-900"></p>
                </div>
                <div class="mb-4">
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">Komisyon Oranı (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="iyzico_commission_rate" id="commission_rate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        İptal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(schoolId, schoolName, currentRate) {
    document.getElementById('modalTitle').textContent = 'Komisyon Oranı Düzenle';
    document.getElementById('schoolName').textContent = schoolName;
    document.getElementById('commission_rate').value = currentRate;
    document.getElementById('commissionForm').action = '/superadmin/commission/' + schoolId;
    document.getElementById('commissionModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('commissionModal').classList.add('hidden');
}
</script>
@endsection
