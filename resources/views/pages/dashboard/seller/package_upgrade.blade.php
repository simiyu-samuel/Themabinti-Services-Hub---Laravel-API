<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upgrade Package') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-center mb-8">Choose Your Plan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Basic Plan --}}
                        <div class="border border-gray-200 rounded-lg p-6 text-center shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                            <h4 class="text-xl font-semibold mb-2">Basic</h4>
                            <p class="text-4xl font-bold mb-4">$10<span class="text-lg font-normal">/mo</span></p>
                            <ul class="text-left space-y-2 mb-6 text-gray-700">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Feature 1</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Feature 2</li>
                                <li class="flex items-center text-gray-400"><svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Feature 3</li>
                            </ul>
                            <button class="bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md w-full cursor-not-allowed">Current Plan</button>
                        </div>

                        {{-- Pro Plan --}}
                        <div class="border-primary border-2 rounded-lg p-6 text-center shadow-lg transition duration-300 ease-in-out transform hover:scale-105 relative">
                            <span class="bg-primary text-white text-xs font-bold px-3 py-1 rounded-full absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">Most Popular</span>
                            <h4 class="text-xl font-semibold mb-2">Pro</h4>
                            <p class="text-4xl font-bold mb-4">$25<span class="text-lg font-normal">/mo</span></p>
                            <ul class="text-left space-y-2 mb-6 text-gray-700">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Feature 1</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Feature 2</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Feature 3</li>
                            </ul>
                            <button class="bg-primary text-white font-bold py-2 px-4 rounded-md w-full hover:bg-primary-dark transition duration-300">Upgrade to Pro</button>
                        </div>

                        {{-- Enterprise Plan --}}
                        <div class="border border-gray-200 rounded-lg p-6 text-center shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                            <h4 class="text-xl font-semibold mb-2">Enterprise</h4>
                            <p class="text-4xl font-bold mb-4">$50<span class="text-lg font-normal">/mo</span></p>
                            <ul class="text-left space-y-2 mb-6 text-gray-700">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>All Pro Features</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Dedicated Support</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Advanced Analytics</li>
                            </ul>
                            <button class="bg-primary text-white font-bold py-2 px-4 rounded-md w-full hover:bg-primary-dark transition duration-300">Contact Us</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

