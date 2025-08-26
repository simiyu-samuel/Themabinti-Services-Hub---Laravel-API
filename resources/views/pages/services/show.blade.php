<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                    {{-- Service Images/Gallery --}}
                    <div>
                        <img src="https://via.placeholder.com/800x600?text=Service+Image" alt="Service Image" class="w-full h-auto object-cover rounded-lg shadow-xl border border-gray-200">
                        {{-- Potentially add a small gallery here if multiple images are supported --}}
                    </div>

                    {{-- Service Details --}}
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">{{ $service->title }}</h1>
                        <p class="text-gray-700 text-lg leading-relaxed mb-6">{{ $service->description }}</p>

                        <div class="flex items-center mb-6">
                            <span class="text-3xl font-bold text-primary mr-4">${{ number_format($service->price, 2) }}</span>
                            <span class="px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-sm font-semibold">Category: {{ $service->category }}</span>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-lg text-gray-800 mb-2">Seller Information</h4>
                            <p class="text-gray-700"><span class="font-semibold">Name:</span> {{ $service->user->name }}</p>
                            <p class="text-gray-700"><span class="font-semibold">Email:</span> {{ $service->user->email }}</p>
                            {{-- Add more seller details like rating, contact info, etc. --}}
                        </div>

                        <button class="w-full bg-accent text-white font-bold py-3 px-6 rounded-md hover:bg-pink-500 transition duration-300 shadow-lg transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                            Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
