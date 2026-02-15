<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Custodian & Finance Approvals</h1>
            <p class="mt-1 text-sm text-gray-500">Approve or reject pending finance and custodian requests.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Finance section --}}
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Pending Finance Requests</h2>
            </div>
            <div class="overflow-x-auto">
                @if($pendingFinance->isEmpty())
                    <p class="p-6 text-gray-500 text-sm">No pending finance requests.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Logistics</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Equipment</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Grand Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted by</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingFinance as $fr)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.events.show', $fr->event) }}" class="text-indigo-600 hover:underline font-medium">{{ $fr->event->title }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">₱{{ number_format($fr->logistics_total, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">₱{{ number_format($fr->equipment_total, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">₱{{ number_format($fr->grand_total, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $fr->submitter->name ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <form action="{{ route('admin.custodian-finance-approvals.finance.update', $fr->event) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="rounded px-3 py-1.5 text-sm font-medium bg-green-600 text-white hover:bg-green-700">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.custodian-finance-approvals.finance.update', $fr->event) }}" method="POST" class="inline-flex items-center gap-2 ml-2">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="rounded px-3 py-1.5 text-sm font-medium bg-red-600 text-white hover:bg-red-700">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Custodian section --}}
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Pending Custodian Requests</h2>
            </div>
            <div class="overflow-x-auto">
                @if($pendingCustodian->isEmpty())
                    <p class="p-6 text-gray-500 text-sm">No pending custodian requests.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingCustodian as $cr)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.events.show', $cr->event) }}" class="text-indigo-600 hover:underline font-medium">{{ $cr->event->title }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cr->custodianMaterial->name ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ $cr->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <form action="{{ route('admin.custodian-finance-approvals.custodian.update', $cr) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="rounded px-3 py-1.5 text-sm font-medium bg-green-600 text-white hover:bg-green-700">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.custodian-finance-approvals.custodian.update', $cr) }}" method="POST" class="inline-flex items-center gap-2 ml-2">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="rounded px-3 py-1.5 text-sm font-medium bg-red-600 text-white hover:bg-red-700">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
