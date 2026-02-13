<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">
                Edit Event Request
            </h2>

            <a href="{{ route('events.show', $event) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10"
         x-data="eventForm({
            venues: {{ json_encode(
                $venues->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'capacity' => $v->capacity,
                    'facilities' => $v->facilities,
                    'locations' => $v->locations->map(fn($l) => [
                        'id' => $l->id,
                        'name' => $l->name,
                        'capacity' => $l->capacity,
                        'amenities' => $l->amenities,
                        'facilities' => $l->facilities,
                        'is_booked' => false,
                    ])->values()
                ])->values()
            ) }},
            existingLocationIds: {{ json_encode($existingVenueLocationIds ?? []) }}
         })">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <form method="POST"
                  action="{{ route('events.update', $event) }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf
                @method('PUT')

                {{-- ================= ERRORS ================= --}}
                @if ($errors->any())
                    <div class="p-5 border-b bg-red-50 border-red-200">
                        <div class="font-bold text-red-700 mb-2">
                            Please fix the following:
                        </div>
                        <ul class="list-disc pl-6 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ================= SUCCESS ================= --}}
                @if(session('success'))
                    <div class="p-5 border-b bg-green-50 border-green-200">
                        <p class="text-sm font-semibold text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                @endif

                <div class="p-6 space-y-10">

                    {{-- ================= BASIC DETAILS ================= --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                            Event Details
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Title --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title', $event->title) }}"
                                       class="w-full rounded-lg border-gray-300"
                                       required>
                            </div>

                            {{-- Venue --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                                <select name="venue_id"
                                        @change="updateVenueCapacity()"
                                        x-model="selected_venue_id"
                                        class="w-full rounded-lg border-gray-300"
                                        required>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}"
                                            {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <p x-show="venue_capacity > 0" class="mt-2 text-xs text-gray-600">
                                    📍 Venue Capacity:
                                    <span class="font-bold text-indigo-600" x-text="venue_capacity"></span> persons
                                </p>
                            </div>

                            {{-- Start --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                                <input type="datetime-local"
                                       name="start_at"
                                       value="{{ old('start_at', $event->start_at->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-lg border-gray-300"
                                       required>
                            </div>

                            {{-- End --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End</label>
                                <input type="datetime-local"
                                       name="end_at"
                                       value="{{ old('end_at', $event->end_at->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-lg border-gray-300"
                                       required>
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="w-full rounded-lg border-gray-300"
                                          required>{{ old('description', $event->description) }}</textarea>
                            </div>

                            {{-- Participants --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Number of Participants
                                    <span x-show="venue_capacity > 0" class="text-xs text-gray-500">
                                        (Max: <span x-text="venue_capacity"></span>)
                                    </span>
                                </label>

                                <input type="number"
                                       name="number_of_participants"
                                       value="{{ old('number_of_participants', $event->number_of_participants) }}"
                                       x-model.number="number_of_participants"
                                       @input="validateParticipants()"
                                       min="0"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Expected number of participants">

                                <p x-show="participants_error"
                                   class="mt-2 text-sm text-red-600"
                                   x-text="participants_error"></p>
                            </div>

                            {{-- Locations --}}
                            <div class="md:col-span-2" x-show="selected_venue_id">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Venue Locations
                                </label>

                                {{-- Hidden JSON backup --}}
                                <input type="hidden"
                                       name="venue_location_ids_json"
                                       :value="JSON.stringify(selected_venue_location_ids)">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <template x-for="location in availableLocations" :key="location.id">
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer transition"
                                               :class="location.is_booked
                                                    ? 'border-red-200 bg-red-50/40 cursor-not-allowed'
                                                    : 'border-gray-200 hover:bg-indigo-50'">

                                            {{-- IMPORTANT FIX: name="venue_location_ids[]" --}}
                                            <input type="checkbox"
                                                   name="venue_location_ids[]"
                                                   :value="String(location.id)"
                                                   x-model="selected_venue_location_ids"
                                                   @change="updateLocationCapacity(); validateParticipants()"
                                                   :disabled="location.is_booked"
                                                   class="mt-1">

                                            <div class="ml-3 flex-1">
                                                <p class="font-semibold text-gray-900" x-text="location.name"></p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Capacity: <span x-text="location.capacity"></span> persons
                                                </p>
                                            </div>
                                        </label>
                                    </template>
                                </div>

                                <div x-show="selected_venue_location_ids.length > 0"
                                     class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <p class="text-sm font-semibold text-indigo-900">
                                        Total Capacity:
                                        <span class="font-black" x-text="location_total_capacity"></span>
                                        persons
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ================= LOGISTICS ================= --}}
                    <div x-data="logisticsRepeater()">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Logistics & Budget
                        </h3>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl border items-end mb-3">

                                <div class="col-span-4">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Resource</label>
                                    <select :name="`logistics_items[${index}][resource_id]`"
                                            x-model="row.resource_id"
                                            class="w-full rounded-lg border-gray-300">
                                        <option value="">-- Select Resource --</option>
                                        @foreach($resources as $resource)
                                            <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                                        @endforeach
                                        <option value="custom">-- Not on list (Manual Entry) --</option>
                                    </select>
                                </div>

                                <div class="col-span-4">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">
                                        Item Name
                                    </label>
                                    <input type="text"
                                           :name="`logistics_items[${index}][resource_name]`"
                                           x-model="row.resource_name"
                                           class="w-full rounded-lg border-gray-300"
                                           placeholder="If custom, type the name">
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Quantity</label>
                                    <input type="number"
                                           min="1"
                                           :name="`logistics_items[${index}][quantity]`"
                                           x-model.number="row.quantity"
                                           class="w-full rounded-lg border-gray-300">
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Unit Price (₱)</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           :name="`logistics_items[${index}][unit_price]`"
                                           x-model.number="row.unit_price"
                                           class="w-full rounded-lg border-gray-300">
                                </div>

                                <div class="col-span-11 text-right font-bold">
                                    ₱<span x-text="formatNumber(row.quantity * row.unit_price)"></span>
                                </div>

                                <div class="col-span-1 text-right">
                                    <button type="button"
                                            @click="removeRow(index)"
                                            class="text-red-500 font-bold">
                                        ✕
                                    </button>
                                </div>

                            </div>
                        </template>

                        <button type="button"
                                @click="addRow()"
                                class="text-indigo-600 text-sm font-bold">
                            + Add Item
                        </button>

                        <div class="mt-4 text-right font-bold text-xl text-indigo-600">
                            ₱<span x-text="formatNumber(calculateTotal())"></span>
                        </div>
                    </div>

                    {{-- ================= CUSTODIAN ================= --}}
                    <div x-data="custodianRepeater()">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Custodian Equipment
                        </h3>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-3 gap-4 mb-3">

                                <select :name="`custodian_items[${index}][material_id]`"
                                        x-model="row.material_id"
                                        class="rounded-lg border-gray-300">
                                    <option value="">Select</option>
                                    @foreach($custodianMaterials as $mat)
                                        <option value="{{ $mat->id }}">
                                            {{ $mat->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="number"
                                       min="1"
                                       :name="`custodian_items[${index}][quantity]`"
                                       x-model.number="row.quantity"
                                       class="rounded-lg border-gray-300">

                                <button type="button"
                                        @click="removeRow(index)"
                                        class="text-red-600 font-semibold">
                                    Remove
                                </button>
                            </div>
                        </template>

                        <button type="button"
                                @click="addRow()"
                                class="text-indigo-600 text-sm font-bold">
                            + Add Equipment
                        </button>
                    </div>

                    {{-- ================= COMMITTEE ================= --}}
                    <div x-data="committeeRepeater()">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Committee
                        </h3>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-2 gap-4 mb-3">

                                <select :name="`committee[${index}][employee_id]`"
                                        x-model="row.employee_id"
                                        class="rounded-lg border-gray-300">
                                    <option value="">Select</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->last_name }}, {{ $emp->first_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="text"
                                       :name="`committee[${index}][role]`"
                                       x-model="row.role"
                                       class="rounded-lg border-gray-300"
                                       placeholder="Role (ex. Head, Assistant)">
                            </div>
                        </template>

                        <button type="button"
                                @click="addRow()"
                                class="text-indigo-600 text-sm font-bold">
                            + Add Member
                        </button>
                    </div>

                </div>

                <div class="px-6 py-4 border-t bg-gray-50 text-right">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase">
                        Update Request
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('eventForm', (venuesData) => ({
                venues: venuesData?.venues || [],
                existingLocationIds: venuesData?.existingLocationIds || [],

                selected_venue_id: '{{ old('venue_id', $event->venue_id) }}',
                venue_capacity: {{ $event->venue->capacity ?? 0 }},
                number_of_participants: {{ old('number_of_participants', $event->number_of_participants ?? 0) }},
                participants_error: '',

                selected_venue_location_ids: [],
                availableLocations: [],
                location_total_capacity: 0,

                updateVenueCapacity() {
                    const venue = this.venues.find(v => String(v.id) === String(this.selected_venue_id));
                    this.venue_capacity = venue ? Number(venue.capacity || 0) : 0;
                    this.availableLocations = venue ? (venue.locations || []) : [];

                    // Reset selection on venue change
                    this.selected_venue_location_ids = [];
                    this.location_total_capacity = 0;

                    this.updateLocationCapacity();
                    this.validateParticipants();
                },

                updateLocationCapacity() {
                    let total = 0;

                    this.availableLocations.forEach(loc => {
                        const id = String(loc.id);
                        if (this.selected_venue_location_ids.includes(id)) {
                            total += Number(loc.capacity || 0);
                        }
                    });

                    this.location_total_capacity = total;
                },

                validateParticipants() {
                    this.participants_error = '';

                    const maxCap = this.selected_venue_location_ids.length > 0
                        ? this.location_total_capacity
                        : this.venue_capacity;

                    if (maxCap > 0 && this.number_of_participants > maxCap) {
                        this.participants_error = `Number of participants cannot exceed capacity of ${maxCap}.`;
                    }
                },

                init() {
                    const venue = this.venues.find(v => String(v.id) === String(this.selected_venue_id));
                    this.availableLocations = venue ? (venue.locations || []) : [];

                    // Load selected locations from DB
                    this.selected_venue_location_ids = (this.existingLocationIds || []).map(id => String(id));

                    this.updateLocationCapacity();
                    this.validateParticipants();
                }
            }));

            Alpine.data('logisticsRepeater', () => ({
                rows: {!! json_encode(
                    old('logistics_items',
                        $event->logisticsItems->map(fn($l)=>[
                            'resource_id' => $l->resource_id,
                            'resource_name' => $l->description,
                            'quantity' => $l->quantity,
                            'unit_price' => $l->unit_price,
                        ])->values()
                    )
                ) !!},

                init() {
                    if (!this.rows || this.rows.length === 0) {
                        this.rows = [{resource_id:'', resource_name:'', quantity:1, unit_price:0}];
                    }
                },

                addRow() {
                    this.rows.push({resource_id:'', resource_name:'', quantity:1, unit_price:0});
                },

                removeRow(i) {
                    if (this.rows.length > 1) this.rows.splice(i, 1);
                },

                calculateTotal() {
                    return this.rows.reduce((sum, row) => {
                        return sum + (parseFloat(row.quantity || 0) * parseFloat(row.unit_price || 0));
                    }, 0);
                },

                formatNumber(val) {
                    return new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(val || 0);
                }
            }));

            Alpine.data('custodianRepeater', () => ({
                rows: {!! json_encode(
                    old('custodian_items',
                        $event->custodianRequests->map(fn($c)=>[
                            'material_id' => $c->custodian_material_id,
                            'quantity' => $c->quantity,
                        ])->values()
                    )
                ) !!},

                init() {
                    if (!this.rows || this.rows.length === 0) {
                        this.rows = [{material_id:'', quantity:1}];
                    }
                },

                addRow() {
                    this.rows.push({material_id:'', quantity:1});
                },

                removeRow(i) {
                    if (this.rows.length > 1) this.rows.splice(i, 1);
                }
            }));

            Alpine.data('committeeRepeater', () => ({
                rows: {!! json_encode(
                    old('committee',
                        $event->participants->map(fn($p)=>[
                            'employee_id' => $p->employee_id,
                            'role' => $p->role,
                        ])->values()
                    )
                ) !!},

                init() {
                    if (!this.rows || this.rows.length === 0) {
                        this.rows = [{employee_id:'', role:''}];
                    }
                },

                addRow() {
                    this.rows.push({employee_id:'', role:''});
                },

                removeRow(i) {
                    if (this.rows.length > 1) this.rows.splice(i, 1);
                }
            }));

        });
    </script>
</x-app-layout>
