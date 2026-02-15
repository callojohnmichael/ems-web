<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suggest reschedule: {{ $event->title }}
            </h2>
            <a href="{{ route('events.show', $event) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                ← Back to event
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg border p-6">
                <p class="text-sm text-gray-600 mb-6">
                    Propose new start and end times for this event. Admins will be notified and can accept or decline your suggestion.
                </p>

                @if($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="reschedule-suggestion-form" action="{{ route('events.reschedule-suggestions.store', $event) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="suggested_start_at" class="block text-sm font-medium text-gray-700">Suggested start</label>
                            <input type="datetime-local" name="suggested_start_at" id="suggested_start_at" required
                                   value="{{ old('suggested_start_at') }}"
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="suggested_end_at" class="block text-sm font-medium text-gray-700">Suggested end</label>
                            <input type="datetime-local" name="suggested_end_at" id="suggested_end_at" required
                                   value="{{ old('suggested_end_at') }}"
                                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason (optional)</label>
                            <textarea name="reason" id="reason" rows="3" maxlength="1000"
                                      class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md shadow hover:bg-indigo-700 transition">
                            Submit suggestion
                        </button>
                        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-200 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @endpush
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('reschedule-suggestion-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Submit reschedule suggestion?',
                text: 'Admins will be notified and can accept or decline.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    </script>
    @endpush
</x-app-layout>
