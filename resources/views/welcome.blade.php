<x-app-layout>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    {{-- Unified Theme Configuration --}}
    @php
        $theme = [
            'bg' => 'bg-green-50',
            'text' => 'text-green-700',
            'btn' => 'bg-green-600 hover:bg-green-700',
            'border' => 'border-green-200',
            'icon' => 'text-green-600'
        ];
    @endphp

    <div class="py-12 space-y-16" x-data="{ 
        activeItem: null,
        currentPrice: 0,
        selectedOptions: {},
        
        init() {
            const scrollPos = sessionStorage.getItem('scrollPos');
            if (scrollPos) {
                window.scrollTo(0, parseInt(scrollPos));
                sessionStorage.removeItem('scrollPos');
            }
        },
        saveScroll() {
            sessionStorage.setItem('scrollPos', window.scrollY);
        },
        
        initModal(item) {
            this.activeItem = item;
            this.currentPrice = parseFloat(item.price);
            this.selectedOptions = {};
            // Pre-select required radio options if needed, or just let user select
            // Actually, we need to track options to sum price
        },
        
        updatePrice(price, type, variationId, isChecked) {
             // Re-calculate total based on form state would be safer but let's try tracking state
             // Simplified: Just recalculate from all checked inputs whenever a change happens
             this.$nextTick(() => {
                 let total = parseFloat(this.activeItem.price);
                 const form = document.getElementById('add-to-cart-form');
                 if(form) {
                     const formData = new FormData(form);
                     // Iterate over all inputs in the form
                     // Since we don't have easy access to option prices in FormData keys, 
                     // we need to rely on data attributes on inputs.
                     
                     // Alternative: bind inputs to models, but variable variation depth makes x-model hard.
                     
                     // Let's use querySelectorAll on checked inputs
                     const checked = form.querySelectorAll('input:checked, select option:checked');
                     checked.forEach(el => {
                         // For select option, the element is the option, but the value is id. 
                         // We need price. Let's add data-price to inputs/options.
                         // For select: parent select has name, selected option has data-price
                         if(el.tagName === 'OPTION') {
                             if(el.dataset.price) total += parseFloat(el.dataset.price);
                         } else {
                             if(el.dataset.price) total += parseFloat(el.dataset.price);
                         }
                     });
                 }
                 this.currentPrice = total.toFixed(2);
             });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-6">
                @if(session('success'))
                    <div
                        class="mb-8 p-4 bg-green-100 text-green-800 rounded-xl shadow-sm border border-green-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div
                        class="mb-8 p-4 bg-red-100 text-red-800 rounded-xl shadow-sm border border-red-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @foreach($categories as $category)
                    @if($category->items->count() > 0)
                        <!-- Category Section with Unified Green Theme -->
                        <div class="mb-8 relative group rounded-3xl px-6 py-5 {{ $theme['bg'] }} border {{ $theme['border'] }}"
                            x-data="{
                                                                                                        scrollLeft() {
                                                                                                            $refs.container.scrollBy({ left: -320, behavior: 'smooth' });
                                                                                                        },
                                                                                                        scrollRight() {
                                                                                                            $refs.container.scrollBy({ left: 320, behavior: 'smooth' });
                                                                                                        }
                                                                                                     }">

                            <!-- Category Header -->
                            <div class="flex justify-between items-center mb-4 border-b border-green-200 pb-3">
                                <h3 class="text-2xl font-extrabold {{ $theme['text'] }} tracking-tight flex items-center gap-2">
                                    {{ $category->name }}
                                </h3>
                                <div class="flex items-center gap-4">
                                    <div class="flex gap-2">
                                        <button @click="scrollLeft()"
                                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center {{ $theme['icon'] }} hover:bg-green-50 hover:shadow-md transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <button @click="scrollRight()"
                                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center {{ $theme['icon'] }} hover:bg-green-50 hover:shadow-md transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Scroll Container -->
                            <div x-ref="container"
                                class="flex overflow-x-auto gap-6 pb-2 no-scrollbar scroll-smooth snap-x snap-mandatory -mx-2 px-2">
                                @foreach($category->items as $item)
                                    <div
                                        class="flex-none w-80 snap-start bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-green-100">
                                        @if($item->image_path)
                                            <div class="relative h-48 overflow-hidden group-hover:opacity-95 transition-opacity">
                                                <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : Storage::url($item->image_path) }}"
                                                    alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                <div
                                                    class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm">
                                                    <span class="text-sm font-bold {{ $theme['text'] }}">${{ $item->price }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-400">No Image</span>
                                            </div>
                                        @endif

                                        <div class="p-5 flex flex-col justify-end h-[160px]">
                                            <div>
                                                <h4 class="text-xl font-bold text-gray-900 leading-tight mb-2 line-clamp-1">
                                                    {{ $item->name }}
                                                </h4>
                                                <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 h-[40px]">
                                                    {{ $item->description }}
                                                </p>
                                            </div>

                                            <div class="mt-2 flex gap-3 justify-end items-center">
                                                <!-- Quick View Button -->
                                                <button type="button" @click='initModal(@json($item, JSON_HEX_APOS))'
                                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 h-9 px-3 rounded-md transition-colors flex items-center justify-center"
                                                    title="Quick View">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Add/Quantity Button -->
                                                <div>
                                                    @if(isset(session('cart')[$item->id]))
                                                        <div
                                                            class="flex items-center justify-between {{ $theme['bg'] }} rounded-md overflow-hidden border {{ $theme['border'] }} h-9">
                                                            <a href="{{ route('cart.decrease', $item->id) }}" @click="saveScroll()"
                                                                class="pl-3 pr-2 {{ $theme['text'] }} hover:bg-green-100 transition-colors font-bold text-base h-full flex items-center">
                                                                −
                                                            </a>
                                                            <span
                                                                class="font-bold {{ $theme['text'] }} text-sm px-2">{{ session('cart')[$item->id]['quantity'] }}</span>
                                                            <a href="{{ route('cart.add', $item) }}" @click="saveScroll()"
                                                                class="pl-2 pr-3 {{ $theme['text'] }} hover:bg-green-100 transition-colors font-bold text-base h-full flex items-center">
                                                                +
                                                            </a>
                                                        </div>
                                                    @else
                                                        @if($item->variations->isEmpty())
                                                            <form action="{{ route('cart.add', $item) }}" method="GET" class="h-full">
                                                                <button type="submit" @click="saveScroll()"
                                                                    class="{{ $theme['btn'] }} text-white font-bold h-9 px-4 rounded-md shadow-sm transition-transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M12 4v16m8-8H4" />
                                                                    </svg>
                                                                    Add
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" @click='initModal(@json($item, JSON_HEX_APOS))'
                                                                class="{{ $theme['btn'] }} text-white font-bold h-9 px-4 rounded-md shadow-sm transition-transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M12 4v16m8-8H4" />
                                                                </svg>
                                                                Add
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($categories->isEmpty())
                    <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <p class="text-xl text-gray-500 font-medium">Menu is currently empty.</p>
                        <p class="text-gray-400 mt-2">Please check back later for our delicious offerings!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Flowbite Quick View Modal -->
        <!-- Main modal -->
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
                        class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white z-10"
                        data-modal-hide="crypto-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>

                    <!-- Image Side (Flowbite Grid/Flex) -->
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
                                <!-- Variations Logic (Same as before but wrapped in strict divs) -->
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
                                                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                                    :required="variation.required"
                                                                    :data-price="option.price" @change="updatePrice()">
                                                                <label :for="'option-' + option.id"
                                                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
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
                                                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                                    :data-price="option.price" @change="updatePrice()">
                                                                <label :for="'option-' + option.id"
                                                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
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
                                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
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

                                <button type="submit" @click="saveScroll()"
                                    class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 inline-flex items-center justify-center gap-2">
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