<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-700">This is the admin dashboard. Here you can manage users, services, and all other aspects of the platform.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a3 3 0 015.356-1.857M12 11V9m0 3v3m-4.5-4.5a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Users</h4>
                            <p class="text-gray-600 mb-3">Manage all registered users.</p>
                            <a href="{{ route('admin.users') }}" class="text-primary hover:underline font-medium">Manage Users &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1h-1.25M15 10V5a3 3 0 00-3-3H9a3 3 0 00-3 3v5m3 4h.01M12 15h.01M15 12h.01M9 12h.01M12 18h.01M12 11h.01M12 8h.01M12 5h.01"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Services</h4>
                            <p class="text-gray-600 mb-3">Manage all services.</p>
                            <a href="{{ route('admin.services') }}" class="text-accent hover:underline font-medium">Manage Services &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Appointments</h4>
                            <p class="text-gray-600 mb-3">Manage all appointments.</p>
                            <a href="{{ route('admin.appointments') }}" class="text-primary hover:underline font-medium">Manage Appointments &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Payments</h4>
                            <p class="text-gray-600 mb-3">View all payments.</p>
                            <a href="{{ route('admin.payments') }}" class="text-accent hover:underline font-medium">View Payments &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h10M9 17L4 12l5-5m-2 10l5 5 5-5"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Contacts</h4>
                            <p class="text-gray-600 mb-3">View all contact messages.</p>
                            <a href="{{ route('admin.contacts') }}" class="text-primary hover:underline font-medium">View Contacts &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <div class="p-6 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold mb-1">Blogs</h4>
                            <p class="text-gray-600 mb-3">Manage all blog posts.</p>
                            <a href="{{ route('admin.blogs') }}" class="text-accent hover:underline font-medium">Manage Blogs &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
