<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Message Details') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Message from {{ $contact->name }}</h3>
                    <div class="space-y-2">
                        <p class="text-lg"><strong>Email:</strong> {{ $contact->email }}</p>
                        <p class="text-lg"><strong>Date:</strong> {{ $contact->created_at->format('M d, Y H:i A') }}</p>
                        <hr class="my-4 border-gray-300">
                        <p class="text-lg">{{ $contact->message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
