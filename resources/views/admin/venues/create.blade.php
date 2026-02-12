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
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

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

                    {{-- Facilities --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Facilities & Amenities
                        </label>
                        <textarea name="facilities"
                                  rows="4"
                                  placeholder="e.g., WiFi, Projector, Sound System, Air Conditioning, Parking, Restrooms"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('facilities') }}</textarea>
                        @error('facilities')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Describe the amenities and facilities available at this venue</p>
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

</x-app-layout>
