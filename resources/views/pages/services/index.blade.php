<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Our Services') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- Filters Sidebar --}}
                    <aside class="w-full md:w-1/4 p-4 bg-gray-50 rounded-lg shadow-md">
                        <h3 class="font-bold text-xl mb-6 text-gray-800">Filter Services</h3>
                        <form action="{{ route('services.index') }}" method="GET">
                            {{-- Search Input --}}
                            <div class="mb-5">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search services...">
                            </div>

                            {{-- Category Filter --}}
                            <div class="mb-5">
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                    <option value="">All Categories</option>
                                    <option value="cleaning" {{ request('category') == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                    <option value="plumbing" {{ request('category') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                    <option value="electrical" {{ request('category') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                    {{-- Add more categories dynamically here --}}
                                </select>
                            </div>

                            {{-- Location Filter --}}
                            <div class="mb-5">
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" id="location" value="{{ request('location') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="e.g., Nairobi">
                            </div>

                            {{-- Price Range Filter --}}
                            <div class="mb-5">
                                <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Price Range</label>
                                <div class="flex items-center mt-1">
                                    <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="Min">
                                    <span class="mx-2 text-gray-500">-</span>
                                    <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="Max">
                                </div>
                            </div>

                            {{-- Is Mobile Service Checkbox --}}
                            <div class="mb-6 flex items-center">
                                <input type="checkbox" name="is_mobile" id="is_mobile" value="1" {{ request('is_mobile') ? 'checked' : '' }} class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="is_mobile" class="ml-2 block text-sm text-gray-900">Mobile Service Available</label>
                            </div>

                            <button type="submit" class="w-full bg-primary text-white font-bold py-2 px-4 rounded-md hover:bg-primary-dark transition duration-300 shadow-md">
                                Apply Filters
                            </button>
                            @if(request()->except('page'))
                                <a href="{{ route('services.index') }}" class="w-full block text-center mt-3 text-sm text-gray-600 hover:text-gray-800 hover:underline">Clear Filters</a>
                            @endif
                        </form>
                    </aside>

                    {{-- Services List --}}
                    <main class="w-full md:w-3/4">
                        <h3 class="font-bold text-xl mb-6 text-gray-800">Available Services</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($services as $service)
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 group">
                                    <img src="https://via.placeholder.com/400x250?text=Service" alt="Service Image" class="w-full h-48 object-cover group-hover:opacity-90 transition duration-300">
                                    <div class="p-5">
                                        <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $service->title }}</h3>
                                        <p class="text-gray-600 text-base leading-relaxed mb-3">{{ Str::limit($service->description, 100) }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-lg text-primary">${{ number_format($service->price, 2) }}</span>
                                            <a href="{{ route('services.show', $service) }}" class="text-accent hover:text-pink-600 font-semibold inline-flex items-center">
                                                View Details
                                                <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 col-span-full text-center">No services found matching your criteria.</p>
                            @endforelse
                        </div>

                        <div class="mt-10">
                            {{ $services->links() }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
