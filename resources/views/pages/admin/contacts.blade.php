<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Messages') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="contacts-container" class="min-h-[100px] flex items-center justify-center">
                        <!-- Contacts will be loaded here via JavaScript -->
                        <p class="text-gray-500">Loading contact messages...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('admin.contacts.index') }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const container = document.getElementById('contacts-container');
                    container.innerHTML = ''; // Clear loading text

                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-gray-500">No contact messages found.</p>';
                        return;
                    }

                    const table = document.createElement('table');
                    table.className = 'min-w-full divide-y divide-gray-200';
                    table.innerHTML = `
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200"></tbody>
                    `;

                    const tbody = table.querySelector('tbody');
                    data.forEach(contact => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${contact.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${contact.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(contact.created_at).toLocaleDateString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/admin/contacts/${contact.id}" class="text-primary hover:text-primary-dark">View</a>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    container.appendChild(table);
                })
                .catch(error => {
                    console.error('Error fetching contact messages:', error);
                    const container = document.getElementById('contacts-container');
                    container.innerHTML = '<p class="text-red-500">Could not load contact messages. Please try again later.</p>';
                });
        });
    </script>
    @endpush
</x-app-layout>
