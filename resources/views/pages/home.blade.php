<x-app-layout>
    {{-- Hero Section --}}
    <div class="bg-primary text-white py-24 md:py-32 lg:py-40 relative overflow-hidden">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4 animate-fade-in-up">Find Your Perfect Service</h1>
            <p class="text-lg md:text-xl mb-10 opacity-90 animate-fade-in-up animation-delay-200">The one-stop platform for all your service needs.</p>
            <a href="{{ route('services.index') }}" class="inline-block bg-accent text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-pink-500 transform hover:scale-105 transition duration-300 animate-fade-in-up animation-delay-400">
                Browse Services
            </a>
        </div>
        {{-- Add subtle background elements for visual interest --}}
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice">
                <circle cx="20" cy="20" r="15" fill="currentColor" class="text-white"></circle>
                <circle cx="80" cy="80" r="20" fill="currentColor" class="text-white"></circle>
                <circle cx="50" cy="30" r="10" fill="currentColor" class="text-white"></circle>
            </svg>
        </div>
    </div>

    {{-- Featured Services --}}
    <div class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800">Featured Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- In a real application, you would fetch these from the database --}}
                @for ($i = 0; $i < 3; $i++)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 group">
                        <img src="https://via.placeholder.com/400x250?text=Service+{{ $i + 1 }}" alt="Service Image" class="w-full h-48 object-cover group-hover:opacity-90 transition duration-300">
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-2 text-gray-800">Service Title {{ $i + 1 }}</h3>
                            <p class="text-gray-600 text-base leading-relaxed">A short description of the service. This is a placeholder to give an idea of content length.</p>
                            <div class="mt-5">
                                <a href="#" class="text-primary hover:text-primary-dark font-semibold inline-flex items-center">
                                    Learn More
                                    <svg class="ml-2 w-4 h-4 transform group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('services.index') }}" class="inline-block bg-primary text-white font-bold py-3 px-8 rounded-full shadow-md hover:bg-primary-dark transform hover:scale-105 transition duration-300">
                    View All Services
                </a>
            </div>
        </div>
    </div>

    {{-- Latest Blog Posts --}}
    <div class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-800">From Our Blog</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- In a real application, you would fetch these from the database --}}
                @for ($i = 0; $i < 3; $i++)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 group">
                        <img src="https://via.placeholder.com/400x250?text=Blog+Post+{{ $i + 1 }}" alt="Blog Image" class="w-full h-48 object-cover group-hover:opacity-90 transition duration-300">
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-2 text-gray-800">Blog Post Title {{ $i + 1 }}</h3>
                            <p class="text-gray-600 text-base leading-relaxed">A brief excerpt from the blog post. This is a placeholder to give an idea of content length.</p>
                            <div class="mt-5">
                                <a href="#" class="text-primary hover:text-primary-dark font-semibold inline-flex items-center">
                                    Read More
                                    <svg class="ml-2 w-4 h-4 transform group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('blog.index') }}" class="inline-block bg-accent text-white font-bold py-3 px-8 rounded-full shadow-md hover:bg-pink-500 transform hover:scale-105 transition duration-300">
                    View All Blog Posts
                </a>
            </div>
        </div>
    </div>

    {{-- About Us Section --}}
    <div class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-800">About Themabinti</h2>
            <p class="text-lg md:text-xl text-gray-700 max-w-4xl mx-auto leading-relaxed">
                Themabinti is a platform dedicated to connecting talented service providers with clients who need their skills. Our mission is to make it easy for everyone to find and book the services they need, while empowering professionals to grow their businesses. We believe in fostering a community where quality service meets genuine demand.
            </p>
        </div>
    </div>
</x-app-layout>
