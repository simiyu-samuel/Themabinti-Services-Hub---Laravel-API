<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">
                <img src="https://via.placeholder.com/800x400?text=Blog+Post+Image" alt="Blog Post Image" class="w-full h-auto object-cover rounded-lg shadow-xl border border-gray-200 mb-8">

                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ $post->title }}</h1>

                <div class="flex items-center text-sm text-gray-600 mb-6">
                    <span>Published on {{ $post->published_at->format('F d, Y') }} by</span>
                    <span class="font-semibold ml-1 text-primary">{{ $post->admin->name }}</span>
                </div>

                <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                    {!! $post->body !!}
                </div>

                <div class="mt-8">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center text-primary hover:text-primary-dark font-semibold">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
