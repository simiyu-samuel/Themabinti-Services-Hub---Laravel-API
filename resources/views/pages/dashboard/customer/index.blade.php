<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-700">This is your customer dashboard. Here you can manage your appointments and view your payment history.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">My Appointments</h4>
                            <p class="text-gray-600 mb-3">View and manage your upcoming and past appointments.</p>
                            <a href="{{ route('customer.appointments') }}" class="text-primary hover:underline font-medium">View Appointments &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Payment History</h4>
                            <p class="text-gray-600 mb-3">Review your past transactions and invoices.</p>
                            <a href="{{ route('customer.payments') }}" class="text-accent hover:underline font-medium">View Payments &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
