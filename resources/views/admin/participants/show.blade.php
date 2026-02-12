<x-app-layout>
    <div class="space-y-6">

        {{-- Breadcrumbs --}}
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('admin.participants.index') }}" class="hover:text-blue-600">Events & Participants</a>
            <span>/</span>
            <span class="font-medium text-gray-900">{{ $participant->display_name }}</span>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $participant->display_name }}</h1>
                    @if($participant->type === 'committee')
                        <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 border border-purple-200">
                            Committee Member
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-gray-600">Event: <strong>{{ $event->title }}</strong></p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->isAdmin() || auth()->user()->can('manage participants'))
                    <a href="{{ route('admin.events.participants.edit', [$event, $participant]) }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('admin.participants.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white border p-2 text-gray-400 hover:text-gray-600 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- LEFT COLUMN: Details & Logistics --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Personal Info Card --}}
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold flex items-center gap-2 border-b pb-4">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Personal Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p class="font-semibold text-gray-900">{{ $participant->display_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email Address</p>
                            <p class="font-semibold text-gray-900">{{ $participant->display_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="font-semibold text-gray-900">{{ $participant->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role / Designation</p>
                            <p class="font-semibold text-purple-700">{{ $participant->role ?? 'Standard Participant' }}</p>
                        </div>
                    </div>
                </div>

                {{-- NEW SECTION: Logistics & Materials --}}
                <div class="rounded-lg border bg-white shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                        <h2 class="text-lg font-semibold flex items-center gap-2 text-gray-800">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            Logistics & Materials
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Material Status --}}
                            <div class="rounded-lg border p-4 bg-gray-50">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Materials Issued</h3>
                                <ul class="space-y-2">
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" disabled checked class="rounded text-blue-600"> Event ID Badge
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" disabled {{ $participant->status === 'attended' ? 'checked' : '' }} class="rounded text-blue-600"> Certificate of Attendance
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" disabled class="rounded text-blue-600"> Event Kit / Souvenir
                                    </li>
                                </ul>
                            </div>

                            {{-- Logistics --}}
                            <div class="rounded-lg border p-4 bg-gray-50">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Resource Access</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">WiFi Access:</span>
                                        <span class="font-medium text-green-600">Enabled</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Meal Voucher:</span>
                                        <span class="font-medium text-gray-900">1 x Lunch</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Attendance Logs --}}
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Attendance Logs
                        </span>
                        <span class="text-sm font-normal text-gray-500">{{ $participant->attendances->count() }} check-ins</span>
                    </h2>

                    <div class="overflow-hidden rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-in Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($participant->attendances as $attendance)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $attendance->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ ucfirst($attendance->status ?? 'checked-in') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-400 italic">No records yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Status & Committee --}}
            <div class="space-y-6">

                {{-- Status Card --}}
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-widest text-gray-400">Current Status</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'attended' => 'bg-green-100 text-green-800 border-green-200',
                            'absent' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                    @endphp
                    <div class="rounded-lg border p-4 text-center {{ $statusColors[$participant->status] ?? 'bg-gray-100' }}">
                        <span class="text-xl font-bold uppercase">{{ $participant->status }}</span>
                    </div>
                </div>

                {{-- NEW SECTION: Committee Details (Only shows if type is committee) --}}
                @if($participant->type === 'committee')
                    <div class="rounded-lg border border-purple-200 bg-purple-50 p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-bold uppercase tracking-widest text-purple-600">Committee Assignment</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-purple-400 uppercase">Function</p>
                                <p class="font-bold text-purple-900">{{ $participant->role ?? 'General Logistics' }}</p>
                            </div>
                            <div class="pt-3 border-t border-purple-100">
                                <p class="text-xs text-purple-400 uppercase">Team Members</p>
                                <p class="text-sm text-purple-800">You can list other committee members here by querying the same 'type' from the event.</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Quick Actions --}}
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold">Actions</h2>
                    <div class="space-y-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->can('manage participants'))
                            <a href="{{ route('admin.events.participants.edit', [$event, $participant]) }}" class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Update Information
                            </a>

                            <form method="POST" action="{{ route('admin.events.participants.destroy', [$event, $participant]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 mt-2" onclick="return confirm('Remove participant?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Remove Participant
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>