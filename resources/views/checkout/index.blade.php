<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg order-2 md:order-1">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Customer Details</h3>

                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf

                            @auth
                                <div class="mb-4">
                                    <p class="text-gray-700">Logged in as: <strong>{{ Auth::user()->name }}</strong></p>
                                    <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                                </div>
                            @else
                                <div class="mb-4">
                                    <label for="guest_name" class="block text-gray-700 text-sm font-bold mb-2">Full
                                        Name</label>
                                    <input type="text" name="guest_name" id="guest_name"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required value="{{ old('guest_name') }}">
                                    @error('guest_name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="guest_email" class="block text-gray-700 text-sm font-bold mb-2">Email
                                        Address</label>
                                    <input type="email" name="guest_email" id="guest_email"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required value="{{ old('guest_email') }}">
                                    @error('guest_email') <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="guest_phone" class="block text-gray-700 text-sm font-bold mb-2">Phone
                                        Number</label>
                                    <input type="tel" name="guest_phone" id="guest_phone"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required value="{{ old('guest_phone') }}">
                                    @error('guest_phone') <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endauth

                            <div class="mt-6 border-t pt-4">
                                <label class="flex items-center mb-4">
                                    <input type="checkbox" name="terms" class="form-checkbox h-5 w-5 text-blue-600"
                                        required>
                                    <span class="ml-2 text-gray-700">I agree to the <a href="#"
                                            class="text-blue-500 underline">Terms and Conditions</a></span>
                                </label>
                                @error('terms') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror

                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                                    Place Order (${{ number_format($total, 2) }})
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg order-1 md:order-2 h-fit">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($cart as $item)
                                <li class="py-3 flex justify-between">
                                    <div class="flex items-center">
                                        <span class="text-gray-500 mr-2">{{ $item['quantity'] }}x</span>
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                    <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="border-t mt-4 pt-4 flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('cart.index') }}" class="text-blue-500 text-sm hover:underline">Edit
                                Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>