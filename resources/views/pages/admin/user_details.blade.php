<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">User Details</h3>
                    <div class="space-y-2">
                        <p class="text-lg"><strong>Name:</strong> {{ $user->name }}</p>
                        <p class="text-lg"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="text-lg"><strong>Role:</strong> <span class="px-2 inline-flex text-base leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : ($user->role === 'seller' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')}}">{{ $user->role }}</span></p>
                        {{-- Add more user details here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
