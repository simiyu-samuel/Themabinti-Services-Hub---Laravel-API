<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-light antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ config('app.name', 'Themabinti Services Hub') }}</title>

    <!-- Fonts: Inter from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts (Vite) -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
</head>
<body class="font-sans text-neutral-dark min-h-full flex">

    {{-- Global Flash Message (Toast Notification) --}}
    {{-- Copy the flash message div from layouts/app.blade.php here as well if admin panel needs its own --}}
    {{-- Or keep it in app.blade.php and include app.blade.php in admin views if it's nested (less common for admin) --}}
    {{-- For now, let's assume admin uses its own flash message. --}}
    <div x-data="{
             show: false,
             message: '',
             type: 'success',
             timeout: null,
             init() {
                 window.addEventListener('flash-message', event => {
                     this.show = true;
                     this.message = event.detail.message;
                     this.type = event.detail.type || 'success';
                     clearTimeout(this.timeout);
                     this.timeout = setTimeout(() => this.show = false, 3000);
                 });
             }
         }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-6 right-6 p-4 md:px-6 md:py-4 rounded-lg shadow-xl text-neutral-white z-50 flex items-center space-x-3 text-sm font-semibold"
         :class="{
             'bg-green-600': type === 'success',
             'bg-red-600': type === 'error',
             'bg-blue-600': type === 'info'
         }"
         style="display: none;"
    >
        <svg x-show="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <svg x-show="type === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <svg x-show="type === 'info'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span x-text="message"></span>
    </div>

    {{-- Sidebar --}}
    <aside class="w-64 bg-gradient-to-br from-primary-800 to-primary-900 text-neutral-white flex-shrink-0 h-screen overflow-y-auto shadow-2xl relative">
        <div class="sticky top-0 bg-gradient-to-br from-primary-800 to-primary-900 pb-4 pt-6 px-6 z-10">
            <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-accent-400 to-accent-600">Admin</h2>
            <p class="text-neutral-300 text-sm mt-1">Dashboard & Management</p>
        </div>
        <nav class="mt-6 px-4">
            <a href="/admin/dashboard" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/dashboard') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l7 7m-11 0v10a1 1 0 01-1 1H7a1 1 0 01-1-1v-4m0-2h2m-2 4h2m4-2v2m0 0h2m-2 2h2m-4 2H9a1 1 0 00-1 1v4m8-12h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a1 1 0 01-1 1h-8a1 1 0 01-1-1v-4zm-6 2H6a1 1 0 00-1 1v4m-2-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a1 1 0 01-1 1h-8a1 1 0 01-1-1v-4z"></path></svg>
                Dashboard
            </a>
            <a href="/admin/users" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/users*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M10 20v-2a3 3 0 013-3h.5a3 3 0 013 3v2M3 8a4 4 0 110 5.292M3 16a4 4 0 110 5.292M21 8a4 4 0 110 5.292M21 16a4 4 0 110 5.292"></path></svg>
                Users
            </a>
            <a href="/admin/services" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/services*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Services
            </a>
            <a href="/admin/appointments" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/appointments*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Appointments
            </a>
            <a href="/admin/payments" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/payments*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Payments
            </a>
            <a href="/admin/contacts" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/contacts*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-6 13h6a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2h6l-3 4"></path></svg>
                Contacts
            </a>
            <a href="/admin/blogs" class="flex items-center px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group {{ Request::is('admin/blogs*') ? 'bg-primary-700 text-accent-300' : '' }}">
                <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v6m2 2l-3-3m0 0l-3 3m3-3v8"></path></svg>
                Blog
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full mt-6">
                @csrf
                <button type="submit" class="flex items-center w-full text-left px-4 py-3 rounded-lg text-neutral-white hover:bg-primary-700 hover:text-accent-300 transition duration-300 text-lg font-medium group">
                    <svg class="h-6 w-6 mr-4 text-primary-300 group-hover:text-accent-300 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    {{-- Main Content Area --}}
    <div class="flex-grow p-8 bg-neutral-light overflow-auto">
        {{-- Topbar for Admin Page --}}
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-neutral-300 mb-6 sticky top-0 bg-neutral-light z-30 -mx-8 px-8">
            <h1 class="text-4xl font-extrabold text-primary-800 mb-2 sm:mb-0">{{ $title ?? 'Admin Panel' }}</h1>
            <div class="text-neutral-dark text-lg flex items-center space-x-2">
                <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Welcome, <span class="font-semibold text-primary-700">{{ Auth::user()->name }}</span>!</span>
            </div>
        </header>

        @yield('content')
    </div>

</body>
</html>