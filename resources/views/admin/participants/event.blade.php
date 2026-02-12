<x-app-layout>
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
                    <a href="{{ route('admin.events.participants.create', $event) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">+ Add Participant</a>
                    <a href="{{ route('admin.participants.index') }}" class="px-3 py-1.5 border rounded text-sm">Back</a>
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
                        <label class="text-sm font-medium">Select Employee (optional)</label>
                            <select name="employee_id" id="employee_id" class="mt-1 block w-full border rounded px-2 py-1">
                                <option value="">-- Manual Entry --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                            data-name="{{ $emp->full_name }}"
                                            data-email="{{ $emp->email ?? '' }}"
                                            data-phone="{{ $emp->phone ?? '' }}"
                                            {{ old('employee_id') == $emp->id ? 'selected' : '' }}> 
                                        {{ $emp->full_name }} {{ $emp->email ? '(' . $emp->email . ')' : '' }}
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
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Add Participant</button>
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
                                        <a href="{{ route('admin.events.participants.edit', [$event, $member]) }}" class="text-purple-600 hover:text-purple-900 text-sm font-medium">Edit</a>
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
                    <a href="{{ route('admin.events.participants.index', $event) }}" class="text-sm font-medium text-blue-600">View All →</a>
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
                                        <a href="{{ route('admin.events.participants.show', [$event, $participant]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const empSelect = document.getElementById('employee_id');
            if (!empSelect) return;

            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.querySelector('input[name="phone"]');

            function applyEmpData(opt) {
                if (!opt) return;
                const name = opt.dataset.name || '';
                const email = opt.dataset.email || '';
                const phone = opt.dataset.phone || '';
                if (nameInput) nameInput.value = name;
                if (emailInput) emailInput.value = email;
                if (phoneInput) phoneInput.value = phone;
            }

            // On change, fill inputs
            empSelect.addEventListener('change', function (e) {
                const val = e.target.value;
                if (!val) {
                    // manual entry: clear fields
                    if (nameInput) nameInput.value = '';
                    if (emailInput) emailInput.value = '';
                    if (phoneInput) phoneInput.value = '';
                    return;
                }
                const opt = empSelect.querySelector('option[value="' + CSS.escape(val) + '"]');
                applyEmpData(opt);
            });

            // If an employee was pre-selected (old input), apply its data on load
            const initialVal = empSelect.value;
            if (initialVal) {
                const opt = empSelect.querySelector('option[value="' + CSS.escape(initialVal) + '"]');
                applyEmpData(opt);
            }
        });
    </script>
    </div>
</x-app-layout>
