<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="space-y-6">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('admin.participants.index') }}" class="hover:text-blue-600">Participants</a>
            <span>/</span>
            <span class="font-medium text-gray-900">Add to {{ $event->title }}</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Participant</h1>
                <p class="mt-1 text-gray-600">Event: <span class="font-semibold text-gray-800">{{ $event->title }}</span></p>
            </div>
            <a href="{{ route('admin.participants.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50/50 px-8 py-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Participant Details</h2>
            </div>

            <form action="{{ route('admin.events.participants.store', $event) }}" method="POST" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Searchable User Selection --}}
                    <div class="md:col-span-2">
                        <label for="employee_id" class="block text-sm font-medium text-gray-900">Search Employee</label>
                        <select name="employee_id" id="employee_id" placeholder="Start typing a name or email..." autocomplete="off">
                            <option value="">-- Manual Entry (Not an employee) --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" 
                                        data-name="{{ $emp->full_name }}" 
                                        data-email="{{ $emp->email ?? '' }}"
                                        {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }} {{ $emp->email ? '(' . $emp->email . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }} @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-900">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-900">Event Role</label>
                        <input type="text" name="role" id="role" value="{{ old('role') }}" placeholder="e.g. Speaker" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-900">Classification</label>
                        @php
                            $onlyCommittee = $onlyCommittee ?? false;
                        @endphp

                        @if($onlyCommittee)
                            <div class="text-sm text-gray-700 mb-2">Event is not published — only committee members can be added.</div>
                            <input type="hidden" name="type" value="committee" />
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">Committee</div>
                        @else
                            <select name="type" id="type" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm">
                                <option value="participant" {{ old('type') == 'participant' ? 'selected' : '' }}>Participant</option>
                                <option value="committee" {{ old('type') == 'committee' ? 'selected' : '' }}>Committee</option>
                            </select>
                        @endif
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-900">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="attended" {{ old('status') == 'attended' ? 'selected' : '' }}>Attended</option>
                            <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.participants.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Confirm & Add Participant
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Initialization Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Searchable Dropdown for employees
            const empSelect = new TomSelect("#employee_id", {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });

            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');

            // Handle Auto-fill
            empSelect.on('change', function(value) {
                const originalOption = document.querySelector(`#employee_id option[value="${value}"]`);
                if (originalOption) {
                    nameInput.value = originalOption.dataset.name || '';
                    emailInput.value = originalOption.dataset.email || '';
                } else {
                    nameInput.value = '';
                    emailInput.value = '';
                }
            });
        });
    </script>

    {{-- Add some Tom Select styling fixes for Tailwind --}}
    <style>
        .ts-control {
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #d1d5db !important;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #3b82f6 !important;
            ring: 2px #3b82f6 !important;
        }
    </style>
</x-app-layout>