<x-app-layout>
    <div class="space-y-8">

        {{-- HEADER --}}
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Published Events — Participants</h1>
                <p class="text-sm text-gray-500">Only published events are listed here. Use the actions to add participants or view event details.</p>
            </div>

            <div class="flex gap-4">
                <div class="bg-white border px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-xs text-gray-500 uppercase font-bold">Total Participants</span>
                    <span class="block text-xl font-black text-blue-600">{{ $totalParticipants }}</span>
                </div>
                <div class="bg-white border px-4 py-2 rounded-lg shadow-sm">
                    <span class="text-xs text-gray-500 uppercase font-bold">Confirmed</span>
                    <span class="block text-xl font-black text-green-600">{{ $totalRegistered }}</span>
                </div>
            </div>
        </div>

        @forelse($events as $event)

            @php
                $committees = $event->participants->where('type', 'committee');
                $participants = $event->participants->where('type', 'participant');
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                {{-- EVENT HEADER --}}
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $event->title }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs font-medium text-gray-500">
                                {{ $event->start_at->format('M d, Y') }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $event->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $event->status }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <div class="text-center">
                            <span class="block text-sm font-bold text-gray-900">{{ $committees->count() }}</span>
                            <span class="text-[10px] text-gray-500 uppercase font-semibold">Committee</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-sm font-bold text-gray-900">{{ $event->participants->count() }}</span>
                            <span class="text-[10px] text-gray-500 uppercase font-semibold">Total Participants</span>
                        </div>

                        <div class="flex items-center space-x-2">
                                <!-- <a href="{{ route('admin.events.participants.create', $event) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-md hover:bg-blue-700 transition">
                                + Add
                            </a> -->

                                <a href="{{ route('admin.events.participants.index', $event) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md">Details</a>

                            <button type="button" onclick="document.getElementById('participants-{{ $event->id }}').classList.toggle('hidden')" class="inline-flex items-center px-3 py-1.5 bg-white border text-xs rounded-md">Toggle participants</button>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div id="participants-{{ $event->id }}" class="overflow-x-auto hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-25 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-gray-600 font-semibold uppercase text-[10px]">Name</th>
                                <th class="px-6 py-3 text-gray-600 font-semibold uppercase text-[10px]">Role/Type</th>
                                <th class="px-6 py-3 text-gray-600 font-semibold uppercase text-[10px]">Status</th>
                                <th class="px-6 py-3 text-right text-gray-600 font-semibold uppercase text-[10px]">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50">

                        {{-- ================= COMMITTEE SECTION ================= --}}
                        @if($committees->count())
                            <tr class="bg-orange-50">
                                <td colspan="4" class="px-6 py-2 text-[11px] font-bold text-orange-700 uppercase tracking-wider">
                                    Committee Members
                                </td>
                            </tr>

                            @foreach($committees as $participant)
                                <tr class="hover:bg-orange-50/40 transition-colors">
                                    <td class="px-6 py-3">
                                        {{-- 🔥 FIXED --}}
                                        <div class="font-medium text-gray-900">{{ $participant->display_name }}</div>

                                        <div class="flex gap-1 mt-1">
                                            @if($participant->user && $participant->user->roles->count())
                                                @foreach($participant->user->roles as $role)
                                                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[9px] font-bold">
                                                        {{ strtoupper($role->name) }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-orange-100 text-orange-700 border border-orange-200">
                                            Committee
                                        </span>
                                        @if($participant->role)
                                            <div class="text-[10px] text-gray-400 mt-0.5 italic">{{ $participant->role }}</div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center text-xs font-semibold {{ $participant->status === 'confirmed' ? 'text-green-600' : 'text-gray-400' }}">
                                            <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $participant->status === 'confirmed' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            {{ ucfirst($participant->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        @if(auth()->user()->isAdmin() || ($event->user_id === auth()->id() && $event->status === 'published'))
                                            <a href="{{ route('admin.events.participants.edit', [$event, $participant]) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- ================= PARTICIPANTS SECTION ================= --}}
                        @if($participants->count())
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-6 py-2 text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                                    Participants
                                </td>
                            </tr>

                            @foreach($participants as $participant)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    {{-- 🔥 FIXED --}}
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $participant->display_name }}</td>

                                    <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-gray-100 text-gray-600">
                                            Participant
                                        </span>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center text-xs font-semibold {{ $participant->status === 'confirmed' ? 'text-green-600' : 'text-gray-400' }}">
                                            <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $participant->status === 'confirmed' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            {{ ucfirst($participant->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        @if(auth()->user()->isAdmin() || ($event->user_id === auth()->id() && $event->status === 'published'))
                                            <a href="{{ route('admin.events.participants.edit', [$event, $participant]) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- EMPTY --}}
                        @if(!$committees->count() && !$participants->count())
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-xs text-gray-400 italic">
                                    No participants registered for this event.
                                </td>
                            </tr>
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>

        @empty
            <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed">
                <p class="text-gray-500">No published events found.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>
</x-app-layout>
