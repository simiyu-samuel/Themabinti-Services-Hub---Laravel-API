<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Payment #{{ $payment->id }}</h3>
                    <div class="space-y-2">
                        <p class="text-lg"><strong>User:</strong> {{ $payment->user->name }}</p>
                        <p class="text-lg"><strong>Amount:</strong> ${{ $payment->amount }}</p>
                        <p class="text-lg"><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                        <p class="text-lg"><strong>Status:</strong> <span class="px-2 inline-flex text-base leading-5 font-semibold rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')}}">{{ $payment->status }}</span></p>
                        <p class="text-lg"><strong>Date:</strong> {{ $payment->created_at->format('M d, Y H:i A') }}</p>
                        {{-- Add more payment details here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
