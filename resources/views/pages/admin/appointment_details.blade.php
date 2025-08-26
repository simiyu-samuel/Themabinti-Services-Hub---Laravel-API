<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment Details') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Appointment #{{ $appointment->id }}</h3>
                    <div class="space-y-2">
                        <p class="text-lg"><strong>Service:</strong> {{ $appointment->service->title }}</p>
                        <p class="text-lg"><strong>Customer:</strong> {{ $appointment->user->name }}</p>
                        <p class="text-lg"><strong>Appointment Date:</strong> {{ $appointment->appointment_date }}</p>
                        <p class="text-lg"><strong>Status:</strong> <span class="px-2 inline-flex text-base leading-5 font-semibold rounded-full {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}}">{{ $appointment->status }}</span></p>
                        {{-- Add more appointment details here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
