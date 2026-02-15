<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Event Request
            </h2>

            <a href="{{ route('events.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    @php
        // Fix: datetime-local requires this exact format
        $oldStart = old('start_at')
            ? \Carbon\Carbon::parse(old('start_at'))->format('Y-m-d\TH:i')
            : '';

        $oldEnd = old('end_at')
            ? \Carbon\Carbon::parse(old('end_at'))->format('Y-m-d\TH:i')
            : '';

        $initialCreateTab = (session('bulk_upload_errors')
            || (session('success') && !old('title') && !old('venue_id') && !old('start_at')))
            ? 'bulk'
            : 'form';
    @endphp

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
                        'facilities' => $l->facilities
                    ])->values()
                ])->values()
            ) }}
         })">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(auth()->user()->isAdmin())
                <div class="mb-6 bg-white shadow-sm rounded-lg border border-gray-200 p-2">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="active_create_tab = 'form'"
                                class="px-4 py-2 rounded-md text-sm font-semibold transition"
                                :class="active_create_tab === 'form' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            Event Form
                        </button>
                        <button type="button"
                                @click="active_create_tab = 'bulk'"
                                class="px-4 py-2 rounded-md text-sm font-semibold transition"
                                :class="active_create_tab === 'bulk' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            Bulk Import
                        </button>
                    </div>
                </div>

                {{-- Bulk Upload Section (Admin Only) --}}
                <div class="mb-8 bg-white shadow-sm rounded-lg"
                     x-show="active_create_tab === 'bulk'"
                     x-cloak>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Bulk Upload Events
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Upload multiple events at once using a CSV file</p>
                    </div>
                    
                    <div class="p-6">
                        {{-- Success/Error Messages --}}
                        @if(session('success'))
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <div class="font-bold mb-2">Please fix the following:</div>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(session('bulk_upload_errors'))
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                                <div class="font-bold mb-2">Upload Errors:</div>
                                <ul class="list-disc list-inside text-sm max-h-40 overflow-y-auto">
                                    @foreach(session('bulk_upload_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Download Template --}}
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h4 class="mt-2 text-lg font-medium text-gray-900">Download CSV Template</h4>
                                <p class="mt-1 text-sm text-gray-500">Get the pre-formatted template with sample data</p>
                                <a href="{{ route('admin.events.bulk-upload-template') }}" 
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-900 focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Template
                                </a>
                            </div>
                            
                            {{-- Upload CSV --}}
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                                <form action="{{ route('admin.events.bulk-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    
                                    <h4 class="text-lg font-medium text-gray-900 text-center">Upload CSV File</h4>
                                    <p class="text-sm text-gray-500 text-center">Fill the template and upload your CSV file</p>
                                    
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-gray-500">CSV files only (MAX. 10MB)</p>
                                            </div>
                                            <input type="file" name="file" class="hidden" accept=".csv,.txt" />
                                        </label>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Upload Events
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        {{-- Instructions --}}
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">📋 Instructions:</h4>
                            <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                                <li>Download the CSV template using the button above</li>
                                <li>Fill in your event data following the format</li>
                                <li>Make sure venue IDs exist in the system</li>
                                <li>Use proper date format: YYYY-MM-DD HH:MM:SS</li>
                                <li>Valid statuses: pending_approvals, approved, published, cancelled, completed</li>
                                <li>Upload the filled CSV file</li>
                            </ol>
                        </div>
                    </div>
                </div>
            @endif

            <div @if(auth()->user()->isAdmin()) x-show="active_create_tab === 'form'" x-cloak @endif>
            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    <div class="font-bold mb-2">Please fix the following:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('events.store') }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200"
                  @submit="syncLocationIds()">
                @csrf

                {{-- Hidden input to capture selected venue location IDs --}}
                <input type="hidden" name="venue_location_ids_json">

                <div class="p-6 space-y-10">

                    {{-- ===================== EVENT DETAILS ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                            Event Details
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Title --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Event Title <span class="text-red-500">*</span>
                                </label>
                                
                                <!-- Smart Title Input with Suggestions -->
                                <div class="relative" x-data="{ 
                                    showSuggestions: false,
                                    suggestions: [
                                        'Annual General Assembly',
                                        'Faculty Meeting',
                                        'Student Council Meeting',
                                        'Parent-Teacher Conference',
                                        'School Foundation Day',
                                        'Graduation Ceremony',
                                        'Recognition Day',
                                        'Sports Fest',
                                        'Intramurals',
                                        'Science Fair',
                                        'Math Quiz Bee',
                                        'English Spelling Bee',
                                        'Art Exhibit',
                                        'Music Concert',
                                        'Drama Presentation',
                                        'Career Guidance Seminar',
                                        'Leadership Training',
                                        'First Aid Training',
                                        'Fire Drill',
                                        'Earthquake Drill',
                                        'Enrollment Period',
                                        'Orientation Program',
                                        'Welcome Program',
                                        'Farewell Program',
                                        'Christmas Program',
                                        'Foundation Day Celebration',
                                        'Teachers Day Celebration',
                                        'Nutrition Month Celebration',
                                        'Buwan ng Wika Celebration',
                                        'United Nations Celebration',
                                        'World Teachers Day',
                                        'National Heroes Day',
                                        'Bonifacio Day',
                                        'Rizal Day Program',
                                        'Board Meeting',
                                        'Department Meeting',
                                        'PTA Meeting',
                                        'Class Officers Meeting',
                                        'Club Meeting',
                                        'Seminar Workshop',
                                        'Training Session',
                                        'Team Building Activity',
                                        ' Outreach Program',
                                        'Medical Mission',
                                        'Clean-up Drive',
                                        'Tree Planting Activity',
                                        'Blood Donation Drive',
                                        'Vaccination Program',
                                        'Health Awareness Seminar',
                                        'Anti-Bullying Campaign',
                                        'Drug Prevention Seminar',
                                        'Environmental Awareness',
                                        'Recycling Program',
                                        'Energy Conservation Campaign'
                                    ],
                                    filteredSuggestions() {
                                        const query = this.$refs.titleInput?.value.toLowerCase() || '';
                                        if (!query) return this.suggestions.slice(0, 8);
                                        return this.suggestions.filter(s => 
                                            s.toLowerCase().includes(query)
                                        ).slice(0, 8);
                                    },
                                    selectSuggestion(suggestion) {
                                        this.$refs.titleInput.value = suggestion;
                                        this.showSuggestions = false;
                                        this.$refs.titleInput.focus();
                                    }
                                }">
                                    <input type="text"
                                           x-ref="titleInput"
                                           name="title"
                                           value="{{ old('title') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 pr-10"
                                           placeholder="Start typing or choose from suggestions..."
                                           @focus="showSuggestions = true"
                                           @blur="setTimeout(() => showSuggestions = false, 200)"
                                           @input="showSuggestions = true"
                                           required>
                                    
                                    <!-- Dropdown Icon -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    
                                    <!-- Suggestions Dropdown -->
                                    <div x-show="showSuggestions && filteredSuggestions().length > 0"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                                        <div class="py-1">
                                            <template x-for="suggestion in filteredSuggestions()" :key="suggestion">
                                                <button type="button"
                                                        @click="selectSuggestion(suggestion)"
                                                        @mousedown.prevent
                                                        class="w-full px-4 py-2 text-left text-sm hover:bg-purple-50 focus:bg-purple-50 focus:outline-none border-b border-gray-100 last:border-b-0">
                                                    <div class="flex items-center justify-between">
                                                        <span x-text="suggestion"></span>
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                        </svg>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                💡 <span x-text="filteredSuggestions().length"></span> suggestions available • Type to filter
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Choose from common school events or type a custom title
                                </p>
                            </div>

                            {{-- Venue --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Venue <span class="text-red-500">*</span>
                                </label>

                                <select name="venue_id"
                                        x-model="selected_venue_id"
                                        @change="handleVenueChange()"
                                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                        required>
                                    <option value="">-- Select Venue --</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Venue capacity --}}
                                <p x-show="venue_capacity > 0" class="mt-2 text-xs text-gray-600">
                                    📍 Venue Capacity:
                                    <span class="font-bold text-purple-600" x-text="venue_capacity"></span>
                                    persons
                                </p>

                                {{-- Venue details --}}
                                <div x-show="selected_venue_id"
                                     class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Venue Details</h4>

                                    <div class="text-xs text-gray-700 space-y-2" x-show="selectedVenue">
                                        <div>
                                            <span class="font-semibold text-gray-600">Capacity:</span>
                                            <span x-text="selectedVenue ? selectedVenue.capacity + ' persons' : ''"></span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-600">Facilities:</span>
                                            <span x-text="selectedVenue ? (selectedVenue.facilities || 'No facilities listed') : ''"></span>
                                        </div>
                                    </div>

                                    <p x-show="!selectedVenue" class="text-xs text-gray-400">
                                        Venue details loading...
                                    </p>
                                </div>

                                {{-- Availability status --}}
                                <p x-show="availability_checking" class="mt-2 text-xs text-gray-500">
                                    Checking availability…
                                </p>

                                <p x-show="!availability_checking && selected_venue_id && start_at && end_at && !venue_available"
                                   class="mt-2 text-sm text-red-600 font-semibold">
                                    ⚠️ This venue is unavailable for the selected dates.
                                </p>
                            </div>

                            {{-- Start --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Start Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local"
                                       name="start_at"
                                       x-model="start_at"
                                       @change="handleDateChange()"
                                       value="{{ $oldStart }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                       required>
                            </div>

                            {{-- End --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    End Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local"
                                       name="end_at"
                                       x-model="end_at"
                                       @change="handleDateChange()"
                                       value="{{ $oldEnd }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                       required>

                                {{-- Client-side warning (prevents confusion) --}}
                                <p x-show="date_error"
                                   class="mt-2 text-xs text-red-600 font-semibold"
                                   x-text="date_error"></p>
                            </div>

                            {{-- Locations --}}
                            <div class="md:col-span-2" x-show="selected_venue_id">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Venue Locations <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <template x-for="location in availableLocations" :key="location.id">
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer transition"
                                               :class="location.is_booked
                                                    ? 'border-red-200 bg-red-50/40 cursor-not-allowed'
                                                    : 'border-gray-200 hover:bg-purple-50'">

                                            <input type="checkbox"
                                                   name="venue_location_ids[]"
                                                   :value="location.id"
                                                   x-model="selected_venue_location_ids"
                                                   @change="updateLocationCapacity()"
                                                   :disabled="location.is_booked"
                                                   class="mt-1">

                                            <div class="ml-3 flex-1">
                                                <p class="font-semibold text-gray-900" x-text="location.name"></p>

                                                <p class="text-xs text-gray-600 mt-1">
                                                    Capacity: <span x-text="location.capacity"></span> persons
                                                </p>

                                                <p class="text-xs mt-1"
                                                   :class="location.is_booked ? 'text-red-700 font-semibold' : 'text-gray-600'">
                                                    <span x-show="location.is_booked">❌ Not available for selected dates</span>
                                                    <span x-show="!location.is_booked">
                                                        <span x-show="location.amenities">
                                                            Amenities: <span x-text="location.amenities"></span>
                                                        </span>
                                                        <span x-show="!location.amenities">No amenities listed</span>
                                                    </span>
                                                </p>
                                            </div>
                                        </label>
                                    </template>
                                </div>

                                <p x-show="selected_venue_location_ids.length === 0"
                                   class="mt-2 text-xs text-gray-500">
                                    Select at least one location.
                                </p>

                                <div x-show="selected_venue_location_ids.length > 0"
                                     class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                    <p class="text-sm font-semibold text-purple-900">
                                        Total Selected Locations Capacity:
                                        <span class="font-black" x-text="location_total_capacity"></span>
                                        persons
                                    </p>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description"
                                          rows="4"
                                          class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                          placeholder="Event details..."
                                          required>{{ old('description') }}</textarea>
                            </div>

                            {{-- Participants --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Number of Participants
                                    <span x-show="location_total_capacity > 0" class="text-xs text-gray-500">
                                        (Max: <span x-text="location_total_capacity"></span>)
                                    </span>
                                </label>

                                <input type="number"
                                       name="number_of_participants"
                                       value="{{ old('number_of_participants', 0) }}"
                                       x-model.number="number_of_participants"
                                       @input="validateParticipants()"
                                       min="0"
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                       placeholder="Expected number of participants">

                                <p x-show="participants_error"
                                   class="mt-2 text-sm text-red-600 font-semibold"
                                   x-text="participants_error"></p>
                            </div>

                        </div>
                    </div>

                    {{-- ===================== LOGISTICS & AUTOMATIC BUDGET ===================== --}}
                    <div x-data="logisticsRepeater">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Logistics & Budget</h3>
                                <p class="text-xs text-gray-500">
                                    Enter resource details below. Total is calculated automatically.
                                </p>
                            </div>

                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-purple-700 transition">
                                + Add Item
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(row, index) in rows" :key="index">
                                <div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 items-end">

                                    <div class="col-span-12 md:col-span-5">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Resource</label>

                                        <select
                                            :name="`logistics_items[${index}][resource_id]`"
                                            x-model="row.resource_id"
                                            @change="updateResourceName(index)"
                                            class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 logistics-select">

                                            <option value="">-- Select Resource --</option>

                                            @foreach($resources as $resource)
                                                <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                                            @endforeach

                                            @if(!empty($previousLogistics) && $previousLogistics->count())
                                                <optgroup label="Previously requested items">
                                                    @foreach($previousLogistics as $prev)
                                                        <option value="custom:{{ rawurlencode($prev) }}">{{ $prev }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif

                                            <option value="custom">-- Not on list (Manual Entry) --</option>
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-5" x-show="row.resource_id === 'custom'">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Item Name</label>

                                        <input type="text"
                                               :name="`logistics_items[${index}][resource_name]`"
                                               x-model="row.resource_name"
                                               placeholder="e.g., Sound System"
                                               class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                                    </div>

                                    <div class="col-span-6 md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Quantity</label>

                                        <input type="number"
                                               :name="`logistics_items[${index}][quantity]`"
                                               x-model.number="row.quantity"
                                               min="1"
                                               class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                                    </div>

                                    <div class="col-span-6 md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Unit Price (₱)</label>

                                        <input type="number"
                                               step="0.01"
                                               :name="`logistics_items[${index}][unit_price]`"
                                               x-model.number="row.unit_price"
                                               class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                                    </div>

                                    <div class="col-span-10 md:col-span-2 text-right py-2">
                                        <span class="block text-[10px] uppercase font-bold text-gray-400">Subtotal</span>
                                        <span class="text-sm font-bold text-gray-700">
                                            ₱<span x-text="formatNumber(row.quantity * row.unit_price)"></span>
                                        </span>
                                    </div>

                                    <div class="col-span-2 text-right">
                                        <button type="button"
                                                @click="removeRow(index)"
                                                class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                </div>
                            </template>
                        </div>

                        <div class="mt-6 p-4 bg-purple-50 border border-purple-100 rounded-lg flex justify-between items-center">
                            <span class="text-purple-900 font-bold uppercase tracking-wider text-sm">
                                Estimated Total Budget:
                            </span>
                            <span class="text-2xl font-black text-purple-600">
                                ₱<span x-text="formatNumber(calculateTotal())"></span>
                            </span>
                        </div>
                    </div>

                    {{-- ===================== CUSTODIAN EQUIPMENT ===================== --}}
                    <div x-data="custodianRepeater">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Custodian Equipment Request</h3>

                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-purple-700 transition">
                                + Add Equipment
                            </button>
                        </div>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border rounded-lg p-4 mb-3 bg-gray-50/50">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Equipment</label>

                                    <select class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                            :name="`custodian_items[${index}][material_id]`"
                                            x-model="row.material_id">
                                        <option value="">-- Select Equipment --</option>
                                        @foreach($custodianMaterials as $mat)
                                            <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Quantity</label>

                                    <input type="number"
                                           min="1"
                                           class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                           :name="`custodian_items[${index}][quantity]`"
                                           x-model.number="row.quantity">
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
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-purple-700 transition">
                                + Add Member
                            </button>
                        </div>

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4 mb-3 bg-gray-50/50">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Employee</label>

                                    <select class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                            :name="`committee[${index}][employee_id]`"
                                            x-model="row.employee_id">
                                        <option value="">-- Select --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->last_name }}, {{ $emp->first_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Role</label>

                                    <input type="text"
                                           class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                           :name="`committee[${index}][role]`"
                                           x-model="row.role"
                                           placeholder="e.g. Chairperson">
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
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            :disabled="submit_disabled"
                            x-bind:class="submit_disabled ? 'opacity-60 cursor-not-allowed' : ''"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition shadow-sm">
                        Submit Request
                    </button>
                </div>

            </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('eventForm', (venuesData) => ({
                venues: (venuesData && venuesData.venues) ? venuesData.venues : [],
                active_create_tab: @js($initialCreateTab),

                selected_venue_id: "{{ old('venue_id') }}",
                selectedVenue: null,
                venue_capacity: 0,

                // Venue locations
                selected_venue_location_ids: {!! json_encode(old('venue_location_ids', [])) !!},
                location_total_capacity: 0,

                number_of_participants: {{ old('number_of_participants', 0) }},
                participants_error: '',

                // dates
                start_at: @js($oldStart),
                end_at: @js($oldEnd),
                date_error: '',

                // availability
                venue_available: true,
                availability_checking: false,
                availability_conflicts: [],
                availabilityUrlTemplate: "{{ url('/venues/__ID__/availability') }}",

                get submit_disabled() {
                    // disable submit if venue is unavailable OR date is invalid
                    return (this.selected_venue_id && !this.venue_available) || !!this.date_error;
                },

                get availableLocations() {
                    if (!this.selected_venue_id) return [];
                    const selectedIdStr = String(this.selected_venue_id);
                    const venue = this.venues.find(v => String(v.id) === selectedIdStr);
                    if (!venue || !venue.locations) return [];

                    return venue.locations.map(loc => ({
                        ...loc,
                        is_booked: this.checkLocationBooking(loc.id)
                    }));
                },

                checkLocationBooking(locationId) {
                    if (!this.start_at || !this.end_at) return false;
                    return this.availability_conflicts.some(conflict =>
                        String(conflict.venue_location_id) === String(locationId)
                    );
                },

                validateDates() {
                    this.date_error = '';

                    if (!this.start_at || !this.end_at) return;

                    // Compare as real dates in JS (prevents user confusion)
                    const start = new Date(this.start_at);
                    const end = new Date(this.end_at);

                    if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

                    if (end < start) {
                        this.date_error = 'End date/time must be after the start date/time.';
                    }
                },

                updateVenueCapacity() {
                    if (!this.selected_venue_id) {
                        this.selectedVenue = null;
                        this.venue_capacity = 0;
                        this.selected_venue_location_ids = [];
                        this.location_total_capacity = 0;
                        return;
                    }

                    const selectedIdStr = String(this.selected_venue_id);
                    const venue = this.venues.find(v => String(v.id) === selectedIdStr);

                    this.selectedVenue = venue || null;
                    this.venue_capacity = (venue && venue.capacity) ? venue.capacity : 0;

                    // Reset selected locations on venue change
                    this.selected_venue_location_ids = [];
                    this.location_total_capacity = 0;

                    this.validateParticipants();
                },

                updateLocationCapacity() {
                    let total = 0;

                    // Remove any selected locations that are now booked
                    this.selected_venue_location_ids = this.selected_venue_location_ids.filter(id => {
                        const loc = this.availableLocations.find(l => String(l.id) === String(id));
                        return loc && !loc.is_booked;
                    });

                    for (const locationId of this.selected_venue_location_ids) {
                        const location = this.availableLocations.find(loc => String(loc.id) === String(locationId));
                        if (location) total += (parseInt(location.capacity) || 0);
                    }

                    this.location_total_capacity = total;
                    this.validateParticipants();
                },

                validateParticipants() {
                    this.participants_error = '';

                    if (this.location_total_capacity > 0 && this.number_of_participants > this.location_total_capacity) {
                        this.participants_error =
                            `Number of participants cannot exceed selected locations capacity of ${this.location_total_capacity}.`;
                    }
                },

                async checkVenueAvailability() {
                    this.availability_conflicts = [];

                    if (!this.selected_venue_id || !this.start_at || !this.end_at) {
                        this.venue_available = true;
                        this.updateLocationCapacity();
                        return;
                    }

                    this.availability_checking = true;

                    try {
                        const url =
                            this.availabilityUrlTemplate.replace('__ID__', this.selected_venue_id)
                            + '?start_at=' + encodeURIComponent(this.start_at)
                            + '&end_at=' + encodeURIComponent(this.end_at);

                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });

                        if (!res.ok) {
                            this.venue_available = true;
                            this.availability_conflicts = [];
                            this.updateLocationCapacity();
                            return;
                        }

                        const data = await res.json();

                        this.venue_available = !!data.available;
                        this.availability_conflicts = data.conflicts || [];

                        this.updateLocationCapacity();

                    } catch (e) {
                        this.venue_available = true;
                        this.availability_conflicts = [];
                        this.updateLocationCapacity();
                    } finally {
                        this.availability_checking = false;
                    }
                },

                handleVenueChange() {
                    this.updateVenueCapacity();
                    this.validateDates();
                    this.checkVenueAvailability();
                    this.updateLocationCapacity();
                },

                handleDateChange() {
                    this.validateDates();
                    this.checkVenueAvailability();
                    this.updateLocationCapacity();
                },

                init() {
                    // Initial venue
                    if (this.selected_venue_id) {
                        this.updateVenueCapacity();
                    }

                    // Initial dates
                    this.validateDates();

                    // Initial availability
                    if (this.selected_venue_id && this.start_at && this.end_at) {
                        this.checkVenueAvailability();
                    }

                    // Watchers
                    this.$watch('selected_venue_id', () => this.handleVenueChange());
                    this.$watch('start_at', () => this.handleDateChange());
                    this.$watch('end_at', () => this.handleDateChange());
                },

                syncLocationIds() {
                    const jsonInput = document.querySelector('input[name="venue_location_ids_json"]');
                    if (jsonInput) {
                        jsonInput.value = JSON.stringify(this.selected_venue_location_ids);
                    }
                }
            }));

            Alpine.data('logisticsRepeater', () => ({
                rows: {!! json_encode(old('logistics_items', [['resource_id' => '', 'resource_name' => '', 'quantity' => 1, 'unit_price' => 0]])) !!},

                formatNumber(val) {
                    return new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(val || 0);
                },

                addRow() {
                    this.rows.push({ resource_id: '', resource_name: '', quantity: 1, unit_price: 0 });
                    setTimeout(() => { if (window.initLogisticsSelects) window.initLogisticsSelects(); }, 0);
                },

                removeRow(i) {
                    if (this.rows.length > 1) this.rows.splice(i, 1);
                },

                updateResourceName(index) {
                    const val = this.rows[index].resource_id || '';

                    if (val.startsWith('custom:')) {
                        try {
                            const enc = val.substring(7);
                            const decoded = decodeURIComponent(enc);
                            this.rows[index].resource_id = 'custom';
                            this.rows[index].resource_name = decoded;
                        } catch (e) {
                            this.rows[index].resource_id = 'custom';
                            this.rows[index].resource_name = val.substring(7);
                        }
                        return;
                    }

                    if (val && val !== 'custom') {
                        this.rows[index].resource_name = '';
                    }
                },

                calculateTotal() {
                    return this.rows.reduce((sum, row) => {
                        return sum + (parseFloat(row.quantity || 0) * parseFloat(row.unit_price || 0));
                    }, 0);
                }
            }));

            Alpine.data('custodianRepeater', () => ({
                rows: {!! json_encode(old('custodian_items', [['material_id' => '', 'quantity' => 1]])) !!},
                addRow() { this.rows.push({ material_id: '', quantity: 1 }) },
                removeRow(i) { if (this.rows.length > 1) this.rows.splice(i, 1) },
            }));

            Alpine.data('committeeRepeater', () => ({
                rows: {!! json_encode(old('committee', [['employee_id' => '', 'role' => '']])) !!},
                addRow() { this.rows.push({ employee_id: '', role: '' }) },
                removeRow(i) { if (this.rows.length > 1) this.rows.splice(i, 1) },
            }));

        });
    </script>

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
        window.initLogisticsSelects = function () {
            document.querySelectorAll('select.logistics-select').forEach(function (sel) {
                if (sel.dataset.tsInit) return;

                new TomSelect(sel, {
                    create: function (input) {
                        return { value: 'custom:' + encodeURIComponent(input), text: input };
                    },
                    sortField: [{ field: 'text', direction: 'asc' }],
                    plugins: ['clear_button']
                });

                sel.dataset.tsInit = '1';
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (window.initLogisticsSelects) window.initLogisticsSelects();
        });
    </script>

</x-app-layout>

<script>
// File upload UI enhancement
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[type="file"][name="file"]');
    const uploadLabel = fileInput?.parentElement;
    
    if (fileInput && uploadLabel) {
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                uploadLabel.querySelector('p.font-semibold').textContent = fileName;
                uploadLabel.classList.add('border-green-500', 'bg-green-50');
                uploadLabel.classList.remove('border-gray-300', 'bg-gray-50');
            }
        });
        
        // Drag and drop functionality
        uploadLabel.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-500', 'bg-purple-50');
        });
        
        uploadLabel.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
        });
        
        uploadLabel.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        });
    }
});
</script>
