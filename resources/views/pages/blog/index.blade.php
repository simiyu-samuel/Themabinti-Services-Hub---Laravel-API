<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Our Blog') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($posts as $post)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 group">
                            <img src="https://via.placeholder.com/400x250?text=Blog+Post" alt="Blog Image" class="w-full h-48 object-cover group-hover:opacity-90 transition duration-300">
                            <div class="p-5">
                                <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $post->title }}</h3>
                                <p class="text-gray-600 text-base leading-relaxed mb-3">{{ Str::limit($post->excerpt, 150) }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ $post->published_at->format('M d, Y') }}</span>
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-primary hover:text-primary-dark font-semibold inline-flex items-center">
                                        Read More
                                        <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-full text-center">No blog posts found.</p>
                    @endforelse
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
