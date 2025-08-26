<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Blog Management') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.blogs.create') }}" class="bg-primary text-white font-bold py-2 px-4 rounded-md hover:bg-primary-dark transition duration-300">Create Post</a>
                    </div>
                    <div id="blogs-container" class="min-h-[100px] flex items-center justify-center">
                        <!-- Blogs will be loaded here via JavaScript -->
                        <p class="text-gray-500">Loading blog posts...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('admin.blogs.index') }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const container = document.getElementById('blogs-container');
                    container.innerHTML = ''; // Clear loading text

                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-gray-500">No blog posts found.</p>';
                        return;
                    }

                    const table = document.createElement('table');
                    table.className = 'min-w-full divide-y divide-gray-200';
                    table.innerHTML = `
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200"></tbody>
                    `;

                    const tbody = table.querySelector('tbody');
                    data.forEach(post => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${post.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${post.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                    ${post.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/admin/blogs/${post.id}/edit" class="text-primary hover:text-primary-dark">Edit</a>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    container.appendChild(table);
                })
                .catch(error => {
                    console.error('Error fetching blog posts:', error);
                    const container = document.getElementById('blogs-container');
                    container.innerHTML = '<p class="text-red-500">Could not load blog posts. Please try again later.</p>';
                });
        });
    </script>
    @endpush
</x-app-layout>
