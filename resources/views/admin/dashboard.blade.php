<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.categories.index') }}"
                            class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
                            <h3 class="text-lg font-bold">Manage Categories</h3>
                            <p>Add, edit, or delete menu categories.</p>
                        </a>
                        <a href="{{ route('admin.items.index') }}"
                            class="block p-6 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
                            <h3 class="text-lg font-bold">Manage Items</h3>
                            <p>Update menu items, prices, and images.</p>
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                            class="block p-6 bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 transition">
                            <h3 class="text-lg font-bold">View Orders</h3>
                            <p>Check status of incoming orders.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>