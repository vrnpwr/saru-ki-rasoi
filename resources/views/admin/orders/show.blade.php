<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details #') . $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Customer Information</h3>
                            @if($order->user)
                                <p><strong>Name:</strong> {{ $order->user->name }}</p>
                                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            @else
                                <p><strong>Name:</strong> {{ $order->guest_name }} (Guest)</p>
                                <p><strong>Email:</strong> {{ $order->guest_email }}</p>
                                <p><strong>Phone:</strong> {{ $order->guest_phone }}</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-2">Order Information</h3>
                            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i A') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Order Items</h3>
                    <table class="min-w-full leading-normal mb-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Item
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Price
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $orderItem)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            @if($orderItem->item->image_path)
                                                <div class="flex-shrink-0 w-10 h-10">
                                                    <img class="w-full h-full rounded-full object-cover"
                                                        src="{{ Str::startsWith($orderItem->item->image_path, 'http') ? $orderItem->item->image_path : Storage::url($orderItem->item->image_path) }}"
                                                        alt="{{ $orderItem->item->name }}" />
                                                </div>
                                            @endif
                                            <div class="ml-3">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ $orderItem->item->name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            ${{ number_format($orderItem->price, 2) }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $orderItem->quantity }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            ${{ number_format($orderItem->price * $orderItem->quantity, 2) }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:text-blue-800">Back to
                            Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>