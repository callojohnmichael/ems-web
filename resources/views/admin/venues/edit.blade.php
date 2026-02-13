<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Venue: {{ $venue->name }}
            </h2>

            <a href="{{ route('admin.venues.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <form method="POST"
                  action="{{ route('admin.venues.update', $venue) }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    {{-- Venue Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $venue->name) }}"
                               placeholder="e.g., Main Conference Hall"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address"
                                  rows="3"
                                  placeholder="Complete address of the venue"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  required>{{ old('address', $venue->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Venue Facilities --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Facilities
                        </label>
                        <textarea name="facilities"
                                  rows="2"
                                  placeholder="e.g., Parking lot, Reception area, Storage room"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('facilities', $venue->facilities) }}</textarea>
                        @error('facilities')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Total Capacity Display --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">
                            Total Venue Capacity (calculated from locations):
                        </p>
                        <p class="text-2xl font-bold text-indigo-900" id="total-capacity">
                            {{ $venue->capacity }}
                        </p>
                    </div>

                    {{-- Venue Locations --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">
                            Venue Locations (Rooms/Areas) <span class="text-red-500">*</span>
                            <span class="text-xs font-normal text-gray-600">(Minimum 1 required)</span>
                        </label>

                        <div class="space-y-4" id="locations-container">
                            @forelse($venue->locations as $index => $location)
                                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 location-block">

                                    {{-- IMPORTANT: Preserve ID for update --}}
                                    <input type="hidden"
                                           name="locations[{{ $index }}][id]"
                                           value="{{ $location->id }}">

                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Location Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="locations[{{ $index }}][name]"
                                                   value="{{ old("locations.$index.name", $location->name) }}"
                                                   placeholder="e.g., Main Hall, Conference Room A"
                                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Capacity <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number"
                                                   name="locations[{{ $index }}][capacity]"
                                                   value="{{ old("locations.$index.capacity", $location->capacity) }}"
                                                   placeholder="e.g., 100"
                                                   min="1"
                                                   class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-1">
                                            Amenities & Features
                                        </label>
                                        <textarea name="locations[{{ $index }}][amenities]"
                                                  rows="2"
                                                  placeholder="e.g., Projector, Wi-Fi, Tables, Chairs, Sound system, Air conditioning"
                                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old("locations.$index.amenities", $location->amenities) }}</textarea>
                                    </div>

                                    <button type="button"
                                            class="btn-remove-location mt-3 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                        Remove Location
                                    </button>
                                </div>
                            @empty
                                {{-- If no locations exist, show 1 blank --}}
                                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 location-block">
                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Location Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="locations[0][name]"
                                                   placeholder="e.g., Main Hall, Conference Room A"
                                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Capacity <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number"
                                                   name="locations[0][capacity]"
                                                   placeholder="e.g., 100"
                                                   min="1"
                                                   class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-1">
                                            Amenities & Features
                                        </label>
                                        <textarea name="locations[0][amenities]"
                                                  rows="2"
                                                  placeholder="e.g., Projector, Wi-Fi, Tables, Chairs, Sound system, Air conditioning"
                                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <button type="button"
                                id="btn-add-location"
                                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            + Add Another Location
                        </button>
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('admin.venues.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Update Venue
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('locations-container');
            const addBtn = document.getElementById('btn-add-location');
            const totalCapacityEl = document.getElementById('total-capacity');

            let locationIndex = container.querySelectorAll('.location-block').length;

            function updateTotalCapacity() {
                let total = 0;

                container.querySelectorAll('.capacity-input').forEach(input => {
                    total += parseInt(input.value) || 0;
                });

                totalCapacityEl.textContent = total;
            }

            function attachRemoveHandlers() {
                container.querySelectorAll('.btn-remove-location').forEach(btn => {
                    btn.onclick = function (e) {
                        e.preventDefault();

                        const blocks = container.querySelectorAll('.location-block');
                        if (blocks.length <= 1) {
                            alert('At least 1 location is required');
                            return;
                        }

                        btn.closest('.location-block').remove();
                        updateTotalCapacity();
                    };
                });
            }

            function attachCapacityHandlers() {
                container.querySelectorAll('.capacity-input').forEach(input => {
                    input.oninput = updateTotalCapacity;
                    input.onchange = updateTotalCapacity;
                });
            }

            addBtn.addEventListener('click', function (e) {
                e.preventDefault();

                const newBlock = `
                    <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 location-block">

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">
                                    Location Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="locations[${locationIndex}][name]"
                                       placeholder="e.g., Main Hall, Conference Room A"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">
                                    Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="locations[${locationIndex}][capacity]"
                                       placeholder="e.g., 100"
                                       min="1"
                                       class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                Amenities & Features
                            </label>
                            <textarea name="locations[${locationIndex}][amenities]"
                                      rows="2"
                                      placeholder="e.g., Projector, Wi-Fi, Tables, Chairs, Sound system, Air conditioning"
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <button type="button"
                                class="btn-remove-location mt-3 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                            Remove Location
                        </button>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', newBlock);
                locationIndex++;

                attachRemoveHandlers();
                attachCapacityHandlers();
                updateTotalCapacity();
            });

            // Initial setup
            attachRemoveHandlers();
            attachCapacityHandlers();
            updateTotalCapacity();
        });
    </script>
</x-app-layout>
