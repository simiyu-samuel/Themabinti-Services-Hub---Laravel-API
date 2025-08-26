<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Service') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Title --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" value="{{ old('title', $service->title) }}" required>
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" required>{{ old('description', $service->description) }}</textarea>
                        </div>

                        {{-- Price --}}
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="price" id="price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" value="{{ old('price', $service->price) }}" required>
                        </div>

                        {{-- Category --}}
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="cleaning" @selected(old('category', $service->category) == 'cleaning')>Cleaning</option>
                                <option value="plumbing" @selected(old('category', $service->category) == 'plumbing')>Plumbing</option>
                                <option value="electrical" @selected(old('category', $service->category) == 'electrical')>Electrical</option>
                                {{-- Add more categories dynamically here --}}
                            </select>
                        </div>

                        {{-- Media --}}
                        <div class="mb-6">
                            <label for="media" class="block text-sm font-medium text-gray-700">Images/Videos</label>
                            <input type="file" name="media[]" id="media" multiple class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded-md hover:bg-primary-dark transition duration-300">
                                Update Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
