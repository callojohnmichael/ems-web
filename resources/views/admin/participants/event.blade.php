
<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <div class="space-y-6">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('admin.participants.index') }}" class="hover:text-blue-600">Events & Participants</a>
            <span>/</span>
            <span class="font-medium text-gray-900">{{ $event->title }}</span>
        </div>

        <div class="bg-white shadow rounded-lg border">
            <div class="px-6 py-5 flex justify-between items-center bg-gray-50 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                    <p class="text-sm text-gray-500 font-medium">Reference #EVT-{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.events.participants.create', $event) }}" class="px-3 py-1.5 bg-[#9333ea] hover:bg-[#7e22ce] text-white rounded text-sm">+ Add Participant</a>
                    <a href="{{ route('admin.participants.index') }}" class="px-3 py-1.5 border border-[#9333ea] text-[#9333ea] hover:bg-[#9333ea]/10 rounded text-sm">Back</a>
                </div>
            </div>

            <div class="p-6 grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Event Details</h4>
                    <p class="text-sm text-gray-700 mb-4">{{ $event->description ?: 'No description provided.' }}</p>
                    <p class="text-sm"><span class="font-semibold">Requested By:</span> {{ $event->requestedBy->name ?? 'System' }}</p>
                    <p class="text-sm"><span class="font-semibold">Created:</span> {{ $event->created_at->format('M d, Y') }}</p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Schedule & Venue</h4>
                    <p class="text-sm font-semibold text-gray-900">{{ optional($event->venue)->name ?? 'No venue assigned' }}</p>
                    <p class="text-sm text-gray-600">{{ $event->start_at->format('F d, Y g:i A') }} – {{ $event->end_at->format('g:i A') }}</p>
                    <p class="text-sm text-gray-600 mt-3">
                        <span class="font-semibold">Expected:</span> {{ $event->number_of_participants ?? 0 }}
                        <span class="ml-2 font-semibold">Registered:</span> {{ $participantCount }}
                    </p>
                </div>
            </div>
        </div>

    {{-- ADD FORM INLINE --}}
        <div class="bg-white rounded-lg border p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Add Participant</h3>

            <form action="{{ route('admin.events.participants.store', $event) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="participant_lookup" class="text-sm font-medium">Search Employee or User</label>
                        <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">
                        <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                        <select id="participant_lookup" placeholder="Start typing a name or email..." autocomplete="off" class="mt-1 block w-full border rounded">
                            <option value="">-- Manual Entry --</option>
                            @foreach($employees as $emp)
                                @php
                                    $alreadyOnEvent = in_array($emp->id, $existingEmployeeIds ?? []);
                                @endphp
                                <option value="employee:{{ $emp->id }}"
                                        data-name="{{ $emp->full_name }}"
                                        data-email="{{ $emp->email ?? '' }}"
                                        data-phone="{{ $emp->phone_number ?? $emp->mobile_number ?? '' }}"
                                        @if($alreadyOnEvent) disabled @endif
                                        @if(old('employee_id') == (string)$emp->id) selected @endif>
                                    {{ $emp->full_name }}{{ $emp->email ? ' (' . $emp->email . ')' : '' }}{{ $alreadyOnEvent ? ' (Already on the event)' : '' }}
                                </option>
                            @endforeach
                            @foreach($users as $u)
                                @php
                                    $alreadyOnEvent = in_array($u->id, $existingUserIds ?? []);
                                @endphp
                                <option value="user:{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-email="{{ $u->email }}"
                                        data-phone=""
                                        @if($alreadyOnEvent) disabled @endif
                                        @if(old('user_id') == (string)$u->id) selected @endif>
                                    {{ $u->name }} ({{ $u->email }}){{ $alreadyOnEvent ? ' (Already on the event)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm">Full Name</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded px-2 py-1" required />
                        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded px-2 py-1" required />
                        @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" class="mt-1 block w-full border rounded px-2 py-1" />
                    </div>

                    <div>
                        <label class="text-sm">Role</label>
                        <input name="role" value="{{ old('role') }}" class="mt-1 block w-full border rounded px-2 py-1" />
                    </div>

                    <div>
                        <label class="text-sm">Type</label>
                        @php $onlyCommittee = ($event->status !== 'published' && !auth()->user()->isAdmin()); @endphp

                        @if($onlyCommittee)
                            <div class="text-sm text-gray-700 mb-2">Event is not published — only committee members can be added.</div>
                            <input type="hidden" name="type" value="committee" />
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">Committee</div>
                        @else
                            <select name="type" class="mt-1 block w-full border rounded px-2 py-1">
                                <option value="participant" {{ old('type') == 'participant' ? 'selected' : '' }}>Participant</option>
                                <option value="committee" {{ old('type') == 'committee' ? 'selected' : '' }}>Committee</option>
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="text-sm">Status</label>
                        <select name="status" class="mt-1 block w-full border rounded px-2 py-1">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="attended" {{ old('status') == 'attended' ? 'selected' : '' }}>Attended</option>
                            <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-[#9333ea] hover:bg-[#7e22ce] text-white rounded">Add Participant</button>
                </div>
            </form>
        </div>

        {{-- COMMITTEE & PARTICIPANTS LIST --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div class="rounded-lg border bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-purple-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-purple-900">Committee Members</h3>
                    <span class="text-purple-700 text-sm font-medium">{{ $committees->count() }} members</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Assignment</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($committees as $member)
                                <tr class="hover:bg-purple-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $member->display_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->display_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">{{ $member->role ?? 'General Committee' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right"> 
                                        <a href="{{ route('admin.events.participants.edit', [$event, $member]) }}" class="text-[#9333ea] hover:text-[#7e22ce] text-sm font-medium">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400 italic">No committee members assigned.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Registered Participants</h3>
                    <a href="{{ route('admin.events.participants.index', $event) }}" class="text-sm font-medium text-[#9333ea] hover:text-[#7e22ce]">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($standardParticipants as $participant)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $participant->display_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $participant->display_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase 
                                            @switch($participant->status)
                                                @case('attended') bg-green-100 text-green-700 @break
                                                @case('absent') bg-red-100 text-red-700 @break
                                                @case('confirmed') bg-blue-100 text-blue-700 @break
                                                @default bg-yellow-100 text-yellow-700
                                            @endswitch">
                                            {{ $participant->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.events.participants.show', [$event, $participant]) }}" class="text-[#9333ea] hover:text-[#7e22ce] text-sm font-medium">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400 italic">No participants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- LOGISTICS SUMMARY FOR ADMINS --}}
        <div class="bg-white rounded-lg border p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Logistics Items</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Assigned</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($event->logisticsItems as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $item->quantity }}× {{ $item->description }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($item->assigned_name)
                                        <div class="font-medium text-gray-900">{{ $item->assigned_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->assigned_email }}</div>
                                    @else
                                        <span class="text-xs italic text-gray-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400 italic">No logistics items.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <style>
        .ts-wrapper { border: none !important; box-shadow: none !important; }
        .ts-wrapper .ts-control { border: 1px solid #d1d5db !important; border-radius: 0.375rem !important; padding: 0.5rem 0.75rem !important; min-height: 2.5rem !important; box-shadow: none !important; }
        .ts-wrapper.focus .ts-control { border-color: #9333ea !important; box-shadow: 0 0 0 2px #9333ea !important; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lookupEl = document.getElementById('participant_lookup');
            if (!lookupEl) return;

            const lookupSelect = new TomSelect('#participant_lookup', {
                create: false,
                sortField: { field: 'text', direction: 'asc' }
            });

            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            const employeeIdInput = document.getElementById('employee_id');
            const userIdInput = document.getElementById('user_id');

            function applyLookup(value) {
                if (!value) {
                    employeeIdInput.value = '';
                    userIdInput.value = '';
                    if (nameInput) nameInput.value = '';
                    if (emailInput) emailInput.value = '';
                    if (phoneInput) phoneInput.value = '';
                    return;
                }
                const opt = document.querySelector('#participant_lookup option[value="' + CSS.escape(value) + '"]');
                if (opt) {
                    if (nameInput) nameInput.value = opt.dataset.name || '';
                    if (emailInput) emailInput.value = opt.dataset.email || '';
                    if (phoneInput) phoneInput.value = opt.dataset.phone || '';
                    if (value.startsWith('employee:')) {
                        employeeIdInput.value = value.slice(9);
                        userIdInput.value = '';
                    } else if (value.startsWith('user:')) {
                        userIdInput.value = value.slice(5);
                        employeeIdInput.value = '';
                    }
                } else {
                    employeeIdInput.value = '';
                    userIdInput.value = '';
                    if (nameInput) nameInput.value = '';
                    if (emailInput) emailInput.value = '';
                    if (phoneInput) phoneInput.value = '';
                }
            }

            lookupSelect.on('change', applyLookup);
            var initialVal = lookupSelect.getValue();
            if (initialVal) applyLookup(initialVal);
        });
    </script>
    </div>
</x-app-layout>
