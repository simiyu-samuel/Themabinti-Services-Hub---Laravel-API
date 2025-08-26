<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Us') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Get in Touch</h1>
                <p class="text-center text-gray-600 mb-8">We'd love to hear from you! Please fill out the form below.</p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200 text-green-700 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('contact') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" value="{{ old('name') }}" required autofocus>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" value="{{ old('email') }}" required>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" id="message" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="text-center pt-4">
                        <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-md hover:bg-primary-dark transition duration-300 shadow-lg transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
