<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Service Details</h3>
                    <div class="space-y-2">
                        <p class="text-lg"><strong>Title:</strong> {{ $service->title }}</p>
                        <p class="text-lg"><strong>Description:</strong> {{ $service->description }}</p>
                        <p class="text-lg"><strong>Price:</strong> ${{ $service->price }}</p>
                        <p class="text-lg"><strong>Seller:</strong> {{ $service->user->name }}</p>
                        <p class="text-lg"><strong>Status:</strong> <span class="px-2 inline-flex text-base leading-5 font-semibold rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span></p>
                        {{-- Add more service details here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
