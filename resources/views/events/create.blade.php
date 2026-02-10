<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Event Request
            </h2>

            <a href="{{ route('events.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="eventForm">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="font-bold mb-2">Fix the following:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('events.store') }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf

                <div class="p-6 space-y-10">

                    {{-- ===================== BASIC EVENT DETAILS ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Event Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                                <select name="venue_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Select Venue --</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                                <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                                <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Event details...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== LOGISTICS & AUTOMATIC BUDGET ===================== --}}
                    <div x-data="logisticsRepeater">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Logistics & Budget</h3>
                                <p class="text-xs text-gray-500">Enter resource details below. Total is calculated automatically.</p>
                            </div>
                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700 transition">
                                + Add Item
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(row, index) in rows" :key="index">
                                <div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-end">
                                    
                                    <div class="col-span-12 md:col-span-5">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Resource Name</label>
                                        <input type="text" 
                                               :name="`logistics_items[${index}][resource_name]`" 
                                               x-model="row.resource_name"
                                               placeholder="e.g., Sound System"
                                               required
                                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div class="col-span-4 md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Quantity</label>
                                        <input type="number" 
                                               :name="`logistics_items[${index}][quantity]`" 
                                               x-model.number="row.quantity"
                                               min="1"
                                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div class="col-span-4 md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Unit Price (₱)</label>
                                        <input type="number" 
                                               step="0.01"
                                               :name="`logistics_items[${index}][unit_price]`" 
                                               x-model.number="row.unit_price"
                                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div class="col-span-3 md:col-span-2 text-right py-2">
                                        <span class="block text-[10px] uppercase font-bold text-gray-400">Subtotal</span>
                                        <span class="text-sm font-bold text-gray-700">
                                            ₱<span x-text="formatNumber(row.quantity * row.unit_price)"></span>
                                        </span>
                                    </div>

                                    <div class="col-span-1 text-right">
                                        <button type="button" @click="removeRow(index)" class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-lg flex justify-between items-center">
                            <span class="text-indigo-900 font-bold uppercase tracking-wider text-sm">Estimated Total Budget:</span>
                            <span class="text-2xl font-black text-indigo-600">
                                ₱<span x-text="formatNumber(calculateTotal())"></span>
                            </span>
                        </div>
                    </div>

                    {{-- ===================== CUSTODIAN EQUIPMENT ===================== --}}
                    <div x-data="custodianRepeater">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Custodian Equipment Request</h3>
                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700 transition">
                                + Add Equipment
                            </button>
                        </div>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border rounded-lg p-4 mb-3 bg-gray-50/50">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Equipment</label>
                                    <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            :name="`custodian_items[${index}][material_id]`" x-model="row.material_id">
                                        <option value="">-- Select Equipment --</option>
                                        @foreach($custodianMaterials as $mat)
                                            <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Quantity</label>
                                    <input type="number" min="1" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                           :name="`custodian_items[${index}][quantity]`" x-model.number="row.quantity">
                                </div>
                                <div class="md:col-span-3 flex justify-end">
                                    <button type="button" @click="removeRow(index)"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-red-700 transition">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ===================== COMMITTEE ===================== --}}
                    <div x-data="committeeRepeater">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Committee</h3>
                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-indigo-700 transition">
                                + Add Member
                            </button>
                        </div>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4 mb-3 bg-gray-50/50">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Employee</label>
                                    <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            :name="`committee[${index}][employee_id]`" x-model="row.employee_id">
                                        <option value="">-- Select --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->last_name }}, {{ $emp->first_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Role</label>
                                    <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                           :name="`committee[${index}][role]`" x-model="row.role" placeholder="e.g. Chairperson">
                                </div>
                                <div class="md:col-span-2 flex justify-end">
                                    <button type="button" @click="removeRow(index)"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-red-700 transition">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            
            // Shared format helper
            Alpine.data('eventForm', () => ({
                formatNumber(val) {
                    return new Intl.NumberFormat('en-PH', { 
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2 
                    }).format(val || 0);
                }
            }));

            // Logistics & Budget Logic
            Alpine.data('logisticsRepeater', () => ({
                // Initial rows from old input or default empty row
                rows: {!! json_encode(old('logistics_items', [['resource_name' => '', 'quantity' => 1, 'unit_price' => 0]])) !!},
                
                addRow() { 
                    this.rows.push({ resource_name: '', quantity: 1, unit_price: 0 }) 
                },
                
                removeRow(i) { 
                    if(this.rows.length > 1) this.rows.splice(i, 1);
                },

                calculateTotal() {
                    return this.rows.reduce((sum, row) => {
                        return sum + (parseFloat(row.quantity || 0) * parseFloat(row.unit_price || 0));
                    }, 0);
                }
            }));

            // Custodian Equipment Logic
            Alpine.data('custodianRepeater', () => ({
                rows: {!! json_encode(old('custodian_items', [['material_id' => '', 'quantity' => 1]])) !!},
                addRow() { this.rows.push({ material_id: '', quantity: 1 }) },
                removeRow(i) { if(this.rows.length > 1) this.rows.splice(i, 1) },
            }));

            // Committee Logic
            Alpine.data('committeeRepeater', () => ({
                rows: {!! json_encode(old('committee', [['employee_id' => '', 'role' => '']])) !!},
                addRow() { this.rows.push({ employee_id: '', role: '' }) },
                removeRow(i) { if(this.rows.length > 1) this.rows.splice(i, 1) },
            }));
        });
    </script>
</x-app-layout>