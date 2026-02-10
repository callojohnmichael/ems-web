<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Event Request') }}: <span class="text-indigo-600">{{ $event->title }}</span>
            </h2>
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                &larr; Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="eventForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 1. BASIC DETAILS --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">1. General Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                            <select name="venue_id" class="w-full rounded-lg border-gray-300">
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Start</label>
                                <input type="datetime-local" name="start_at" value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">End</label>
                                <input type="datetime-local" name="end_at" value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                        </div>
                        {{-- RE-ADDED DESCRIPTION --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" placeholder="Describe the purpose and flow of the event...">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. LOGISTICS REPEATER (Triggers Budget) --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">2. Logistics (Resources)</h3>
                        <button type="button" @click="addRow('logistics')" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-bold uppercase hover:bg-indigo-700">
                            + Add Item
                        </button>
                    </div>
                    <div class="space-y-3">
                        <template x-for="(row, index) in logistics" :key="index">
                            <div class="flex flex-col md:flex-row gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-500">Resource</label>
                                    <select :name="`logistics_items[${index}][resource_id]`" x-model="row.resource_id" @change="updateBudget" class="w-full rounded-md border-gray-300 text-sm">
                                        <option value="">-- Select Resource --</option>
                                        @foreach($resources as $resource)
                                            <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full md:w-32">
                                    <label class="text-[10px] uppercase font-bold text-gray-500">Qty</label>
                                    <input type="number" min="1" :name="`logistics_items[${index}][quantity]`" x-model="row.quantity" @input="updateBudget" class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeRow('logistics', index)" class="p-2 text-red-600 hover:bg-red-50 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 3. CUSTODIAN REPEATER --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-900">3. Custodian Equipment</h3>
                        <button type="button" @click="addRow('custodian')" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-bold uppercase hover:bg-indigo-700">
                            + Add Equipment
                        </button>
                    </div>
                    <div class="space-y-3">
                        <template x-for="(row, index) in custodian" :key="index">
                            <div class="flex flex-col md:flex-row gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label class="text-[10px] uppercase font-bold text-gray-500">Material Name</label>
                                    <select :name="`custodian_items[${index}][material_id]`" x-model="row.material_id" class="w-full rounded-md border-gray-300 text-sm">
                                        <option value="">-- Select Material --</option>
                                        @foreach($custodianMaterials as $material)
                                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full md:w-32">
                                    <label class="text-[10px] uppercase font-bold text-gray-500">Qty</label>
                                    <input type="number" min="1" :name="`custodian_items[${index}][quantity]`" x-model="row.quantity" class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeRow('custodian', index)" class="p-2 text-red-600 hover:bg-red-50 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 4. BUDGET SUMMARY (READ-ONLY) --}}
                <div class="bg-indigo-50/50 shadow-sm rounded-xl border border-indigo-100 p-6">
                    <div class="flex items-center justify-between mb-4 border-b border-indigo-200 pb-2">
                        <h3 class="text-lg font-bold text-indigo-900">4. Financial Summary</h3>
                        <span class="text-[10px] font-black uppercase text-indigo-500 bg-white px-2 py-1 rounded shadow-sm">Locked • Auto-calculated</span>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(item, index) in budget" :key="index">
                            <div class="flex justify-between py-2 border-b border-indigo-100 text-sm italic">
                                <span class="text-gray-600" x-text="item.description"></span>
                                <span class="font-mono font-bold text-gray-900">₱<span x-text="item.amount.toLocaleString(undefined, {minimumFractionDigits: 2})"></span></span>
                                <input type="hidden" :name="`budget_items[${index}][description]`" :value="item.description">
                                <input type="hidden" :name="`budget_items[${index}][amount]`" :value="item.amount">
                            </div>
                        </template>
                        <div class="flex justify-between pt-4 font-bold text-xl text-indigo-700">
                            <span>Estimated Total:</span>
                            <span>₱<span x-text="totalBudget.toLocaleString(undefined, {minimumFractionDigits: 2})"></span></span>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-end space-x-4 bg-white p-6 rounded-xl border border-gray-200">
                    <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Discard Changes</a>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-bold text-sm uppercase tracking-widest hover:bg-indigo-700 shadow-md">
                        Update Event Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function eventForm() {
            return {
                logistics: {!! json_encode(old('logistics_items', $event->resourceAllocations->map(fn($a) => ['resource_id' => $a->resource_id, 'quantity' => $a->quantity]))) !!},
                custodian: {!! json_encode(old('custodian_items', $event->custodianRequests->map(fn($c) => ['material_id' => $c->custodian_material_id, 'quantity' => $c->quantity]))) !!},
                budget: [],
                resourceMap: {!! json_encode($resources->keyBy('id')) !!},

                init() {
                    this.updateBudget();
                },

                addRow(type) {
                    if (type === 'logistics') this.logistics.push({ resource_id: '', quantity: 1 });
                    if (type === 'custodian') this.custodian.push({ material_id: '', quantity: 1 });
                },

                removeRow(type, index) {
                    this[type].splice(index, 1);
                    if (type === 'logistics') this.updateBudget();
                },

                updateBudget() {
                    let newBudget = [];
                    this.logistics.forEach(item => {
                        if (item.resource_id && this.resourceMap[item.resource_id]) {
                            let res = this.resourceMap[item.resource_id];
                            let price = res.price || 0; 
                            newBudget.push({
                                description: `Allocation: ${res.name} (x${item.quantity})`,
                                amount: price * item.quantity
                            });
                        }
                    });
                    this.budget = newBudget;
                },

                get totalBudget() {
                    return this.budget.reduce((sum, item) => sum + item.amount, 0);
                }
            }
        }
    </script>
</x-app-layout>