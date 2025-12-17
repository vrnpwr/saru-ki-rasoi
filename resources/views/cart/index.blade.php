<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Cart') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        activeItem: null,
        currentPrice: 0,
        selectedOptions: {},
        items: {{ json_encode($items) }},

        initModal(item) {
            this.activeItem = item;
            this.currentPrice = parseFloat(item.price);
            this.selectedOptions = {};
        },

        updatePrice() {
             this.$nextTick(() => {
                 let total = parseFloat(this.activeItem.price);
                 const form = document.getElementById('add-to-cart-form');
                 if(form) {
                     const checked = form.querySelectorAll('input:checked, select option:checked');
                     checked.forEach(el => {
                         if(el.dataset.price) total += parseFloat(el.dataset.price);
                     });
                 }
                 this.currentPrice = total.toFixed(2);
             });
        },
        saveScroll() {
            // No scroll save needed for modal add
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count($cart) > 0)
                        <table class="min-w-full leading-normal mb-6">
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
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $details)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex items-center">
                                                <a href="#" @click.prevent="initModal(items[{{ $details['id'] }}])" class="flex items-center hover:opacity-80 transition-opacity">
                                                    @if($details['image_path'])
                                                        <div class="flex-shrink-0 w-10 h-10">
                                                            <img class="w-full h-full rounded-full object-cover"
                                                                src="{{ Str::startsWith($details['image_path'], 'http') ? $details['image_path'] : Storage::url($details['image_path']) }}"
                                                                alt="{{ $details['name'] }}" />
                                                        </div>
                                                    @endif
                                                    <div class="ml-3">
                                                        <p class="text-gray-900 whitespace-no-wrap font-medium">
                                                            {{ $details['name'] }}
                                                        </p>
                                                        @if(isset($details['options']) && is_array($details['options']) && count($details['options']) > 0)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                @foreach($details['options'] as $opt)
                                                                    <div>{{ $opt['variation_name'] }}: {{ $opt['option_name'] }}
                                                                        (+${{ $opt['price'] }})</div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">${{ $details['price'] }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex inline-flex items-center border border-gray-300 rounded overflow-hidden h-9">
                                                <a href="{{ route('cart.decrease', $id) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 transition h-full flex items-center">-</a>
                                                <input type="text" readonly value="{{ $details['quantity'] }}" class="w-12 text-center text-gray-900 border-none focus:ring-0 p-1 text-sm h-full">
                                                <a href="{{ route('cart.increase', $id) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 transition h-full flex items-center">+</a>
                                            </div>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                ${{ $details['price'] * $details['quantity'] }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <a href="{{ route('cart.remove', $id) }}" class="text-red-500 hover:text-red-700 transition" title="Remove Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-right">
                            <h3 class="text-xl font-bold mb-4">Total: ${{ number_format($total, 2) }}</h3>
                            <a href="{{ route('checkout.index') }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Proceed to Checkout
                            </a>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Your cart is currently empty.</p>
                        <div class="text-center">
                            <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-800">Continue Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick View Modal (Re-used) -->
        <div x-show="activeItem" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex justify-center items-center w-full h-full bg-gray-900/50 backdrop-blur-sm p-4 overflow-x-hidden overflow-y-auto md:inset-0">

            <div class="relative w-full max-w-4xl max-h-full" @click.outside="activeItem = null">
                <!-- Modal content -->
                <div
                    class="relative bg-white rounded-lg shadow dark:bg-gray-700 overflow-hidden flex flex-col md:flex-row max-h-[90vh]">

                    <!-- Close button -->
                    <button @click="activeItem = null" type="button"
                        class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white z-10">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>

                    <!-- Image Side -->
                    <div class="md:w-5/12 bg-gray-100 flex items-center justify-center p-4">
                        <template x-if="activeItem?.image_path">
                            <img :src="activeItem.image_path.startsWith('http') ? activeItem.image_path : '/storage/' + activeItem.image_path"
                                :alt="activeItem.name" class="max-w-full max-h-full object-contain rounded-lg">
                        </template>
                        <template x-if="!activeItem?.image_path">
                            <div class="text-gray-400">No Image</div>
                        </template>
                    </div>

                    <!-- Content Side -->
                    <div class="md:w-7/12 p-6 flex flex-col overflow-y-auto">
                        <div class="flex-1">
                            <h3 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white" x-text="activeItem?.name">
                            </h3>
                            <p class="mb-4 text-xl font-bold text-green-600 dark:text-green-400"
                                x-text="'$' + currentPrice"></p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400"
                                x-text="activeItem?.description"></p>

                            <form :action="'/cart/add/' + activeItem?.id" method="GET" id="add-to-cart-form"
                                class="mt-6">
                                <template x-if="activeItem?.variations && activeItem.variations.length > 0">
                                    <div class="mb-6 space-y-4">
                                        <template x-for="variation in activeItem.variations" :key="variation.id">
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-2 text-sm">
                                                    <span x-text="variation.name"></span>
                                                    <span x-show="variation.required" class="text-red-500">*</span>
                                                </h4>

                                                <!-- Radio Type -->
                                                <template x-if="variation.type === 'radio'">
                                                    <div class="space-y-2">
                                                        <template x-for="option in variation.options" :key="option.id">
                                                            <div class="flex items-center">
                                                                <input :id="'option-' + option.id" type="radio"
                                                                    :name="'options[' + variation.id + ']'"
                                                                    :value="option.id"
                                                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 focus:ring-2"
                                                                    :required="variation.required"
                                                                    :data-price="option.price" @change="updatePrice()">
                                                                <label :for="'option-' + option.id"
                                                                    class="ml-2 text-sm font-medium text-gray-900">
                                                                    <span x-text="option.name"></span>
                                                                    <span class="text-gray-500 text-xs"
                                                                        x-show="Number(option.price) > 0"
                                                                        x-text="'(+$' + option.price + ')'"></span>
                                                                </label>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Checkbox Type -->
                                                <template x-if="variation.type === 'checkbox'">
                                                    <div class="space-y-2">
                                                        <template x-for="option in variation.options" :key="option.id">
                                                            <div class="flex items-center">
                                                                <input :id="'option-' + option.id" type="checkbox"
                                                                    :name="'options[' + variation.id + '][]'"
                                                                    :value="option.id"
                                                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                                                    :data-price="option.price" @change="updatePrice()">
                                                                <label :for="'option-' + option.id"
                                                                    class="ml-2 text-sm font-medium text-gray-900">
                                                                    <span x-text="option.name"></span>
                                                                    <span class="text-gray-500 text-xs"
                                                                        x-show="Number(option.price) > 0"
                                                                        x-text="'(+$' + option.price + ')'"></span>
                                                                </label>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Select Type -->
                                                <template x-if="variation.type === 'select'">
                                                    <div>
                                                        <select :name="'options[' + variation.id + ']'"
                                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                                                            :required="variation.required" @change="updatePrice()">
                                                            <option value="" data-price="0">Select Option</option>
                                                            <template x-for="option in variation.options"
                                                                :key="option.id">
                                                                <option :value="option.id" :data-price="option.price"
                                                                    x-text="option.name + (Number(option.price) > 0 ? ' (+$' + option.price + ')' : '')">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <button type="submit"
                                    class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 18 21">
                                        <path
                                            d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>