<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Venue
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

            <form method="POST" action="{{ route('admin.venues.store') }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf

                <div class="p-6 space-y-6">

                    {{-- Venue Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
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
                                  required>{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Capacity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Capacity (Number of Persons) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="capacity"
                               value="{{ old('capacity') }}"
                               placeholder="e.g., 500"
                               min="1"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campuses with Facilities & Amenities --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">
                            Select Campus, Facilities & Amenities
                        </label>
                        <div class="space-y-4">
                            @forelse($campuses as $campus)
                                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50">
                                    {{-- Campus Checkbox --}}
                                    <label class="flex items-center mb-4 cursor-pointer">
                                        <input type="checkbox" 
                                               name="campuses[]" 
                                               value="{{ $campus->id }}"
                                               class="campus-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 cursor-pointer"
                                               data-campus-id="{{ $campus->id }}">
                                        <span class="ml-3 text-sm font-semibold text-gray-900">{{ $campus->name }}</span>
                                        @if($campus->location)
                                            <span class="ml-2 text-xs text-gray-600">({{ $campus->location }})</span>
                                        @endif
                                    </label>

                                    {{-- Facilities for this Campus --}}
                                    <div class="ml-6 space-y-3 campus-facilities" data-campus-id="{{ $campus->id }}">
                                        @forelse($campus->facilities as $facility)
                                            <div class="border border-gray-300 rounded-lg p-3 bg-white">
                                                {{-- Facility Checkbox --}}
                                                <label class="flex items-center mb-2 cursor-pointer">
                                                    <input type="checkbox" 
                                                           name="facilities[{{ $campus->id }}][]" 
                                                           value="{{ $facility->id }}"
                                                           class="facility-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 cursor-pointer"
                                                           data-campus-id="{{ $campus->id }}"
                                                           disabled>
                                                    <span class="ml-3 text-sm font-medium text-gray-800">{{ $facility->name }}</span>
                                                    @if($facility->capacity)
                                                        <span class="ml-2 text-xs text-gray-500">(Capacity: {{ $facility->capacity }})</span>
                                                    @endif
                                                </label>

                                                {{-- Amenities for this Facility --}}
                                                @if($facility->amenities->count())
                                                    <div class="ml-6 mt-2 space-y-2 flex flex-wrap gap-2">
                                                        @foreach($facility->amenities as $amenity)
                                                            <label class="flex items-center cursor-pointer inline-flex px-3 py-1 bg-gray-100 rounded-full text-xs">
                                                                <input type="checkbox" 
                                                                       name="amenities[{{ $facility->id }}][]" 
                                                                       value="{{ $amenity->id }}"
                                                                       class="amenity-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 cursor-pointer"
                                                                       data-facility-id="{{ $facility->id }}"
                                                                       disabled>
                                                                <span class="ml-2 text-gray-700">{{ $amenity->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-500 italic">No facilities available for this campus.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic col-span-full">No campuses available. Please create campuses first.</p>
                            @endforelse
                        </div>
                        @error('campuses')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Additional Facilities Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Facilities & Notes
                        </label>
                        <textarea name="facilities"
                                  rows="3"
                                  placeholder="Any other facilities or special notes about this venue"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('facilities') }}</textarea>
                        @error('facilities')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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
                        Create Venue
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle campus checkbox changes
            document.querySelectorAll('.campus-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const campusId = this.dataset.campusId;
                    const facilitiesContainer = document.querySelector(`.campus-facilities[data-campus-id="${campusId}"]`);
                    
                    if (facilitiesContainer) {
                        const facilityCheckboxes = facilitiesContainer.querySelectorAll('.facility-checkbox');
                        const amenityCheckboxes = facilitiesContainer.querySelectorAll('.amenity-checkbox');
                        
                        // Enable/disable facilities
                        facilityCheckboxes.forEach(cb => {
                            cb.disabled = !this.checked;
                            if (!this.checked) cb.checked = false;
                        });
                        
                        // Enable/disable amenities
                        amenityCheckboxes.forEach(cb => {
                            cb.disabled = !this.checked;
                            if (!this.checked) cb.checked = false;
                        });
                    }
                });
            });

            // Handle facility checkbox changes
            document.querySelectorAll('.facility-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const facilityId = this.dataset.facilityId;
                    const amenityCheckboxes = document.querySelectorAll(`.amenity-checkbox[data-facility-id="${facilityId}"]`);
                    
                    // Enable/disable amenities based on facility selection
                    amenityCheckboxes.forEach(cb => {
                        cb.disabled = !this.checked;
                        if (!this.checked) cb.checked = false;
                    });
                });
            });
        });
    </script>
