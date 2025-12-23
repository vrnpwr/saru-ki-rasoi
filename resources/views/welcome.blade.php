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
            'bg' => 'bg-white',
            'text' => 'text-gray-900',
            'subtext' => 'text-gray-500',
            'accent' => 'text-green-600',
            'btn' => 'bg-white hover:bg-gray-50',
            'btn_text' => 'text-green-700',
            'btn_border' => 'border border-gray-200',
            'card_bg' => 'bg-white',
            'section_bg' => 'bg-gray-50',
        ];
    @endphp

    <!-- Main Container -->
    <div class="bg-gray-50 min-h-screen" x-data="{ 
        activeItem: null,
        currentPrice: 0,
        selectedOptions: {},
        searchQuery: '',
        
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
        },
        
        updatePrice(price, type, variationId, isChecked) {
             this.$nextTick(() => {
                 let total = parseFloat(this.activeItem.price);
                 const form = document.getElementById('add-to-cart-form');
                 if(form) {
                     const checked = form.querySelectorAll('input:checked, select option:checked');
                     checked.forEach(el => {
                         if(el.tagName === 'OPTION') {
                             if(el.dataset.price) total += parseFloat(el.dataset.price);
                         } else {
                             if(el.dataset.price) total += parseFloat(el.dataset.price);
                         }
                     });
                 }
                 this.currentPrice = total.toFixed(2);
             });
        },

        scrollToCategory(id) {
            const el = document.getElementById(id);
            if(el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl shadow-sm border border-green-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- 3-Column Layout -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                <!-- LEFT COLUMN: Categories (Sticky Desktop / Horizontal Mobile) -->
                <div class="md:col-span-3 lg:col-span-2 sticky top-24 z-20">
                    <!-- Desktop/Tablet Sidebar -->
                    <div class="hidden md:block bg-white rounded-2xl shadow-sm p-4 border border-gray-100 max-h-[calc(100vh-8rem)] overflow-y-auto custom-scrollbar">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 px-2">Categories</h2>
                        <nav class="space-y-1">
                            @foreach($categories as $category)
                                @if($category->items->count() > 0)
                                    <button @click="scrollToCategory('category-{{ $category->id }}')" 
                                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-green-600 transition-colors group text-left">
                                        <span class="group-hover:translate-x-1 transition-transform truncate pr-2">{{ $category->name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-500 py-0.5 px-2 rounded-full group-hover:bg-green-100 group-hover:text-green-600 transition-colors flex-shrink-0">
                                            {{ $category->items->count() }}
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </nav>
                    </div>

                    <!-- Mobile Horizontal Scroll (Hidden on MD+) -->
                    <div class="md:hidden -mx-4 px-4 overflow-x-auto no-scrollbar pb-2 flex gap-3 snap-x">
                         @foreach($categories as $category)
                            @if($category->items->count() > 0)
                                <button @click="scrollToCategory('category-{{ $category->id }}')" 
                                    class="snap-start flex-shrink-0 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm text-sm font-bold text-gray-700 whitespace-nowrap active:bg-green-50 active:border-green-500 active:text-green-700 transition-colors">
                                    {{ $category->name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- CENTER COLUMN: Product Feed -->
                <div class="md:col-span-9 lg:col-span-7 space-y-6 pb-24 md:pb-0">
                    
                    <!-- Search Header -->
                    <div class="bg-white p-4 sticky top-0 z-30 md:relative md:top-auto border-b md:border-none border-gray-100 md:rounded-2xl md:shadow-sm">
                        <div class="relative w-full">
                            <input type="text" x-model="searchQuery" placeholder="Search for dishes..." 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500/20 text-sm font-medium">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        
                         <!-- Filters (Veg/Non-Veg) -->
                        <div class="flex items-center gap-3 mt-4">
                            <button class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-full shadow-sm hover:border-gray-300 transition-colors">
                                <span class="border border-green-600 p-[2px] w-3.5 h-3.5 flex items-center justify-center rounded-[2px]">
                                    <div class="bg-green-600 w-1.5 h-1.5 rounded-full"></div>
                                </span>
                                <span class="text-xs font-bold text-gray-700">Veg</span>
                            </button>
                             <button class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-full shadow-sm hover:border-gray-300 transition-colors">
                                <span class="border border-red-600 p-[2px] w-3.5 h-3.5 flex items-center justify-center rounded-[2px]">
                                    <div class="bg-red-600 w-1.5 h-1.5 rounded-full"></div>
                                </span>
                                <span class="text-xs font-bold text-gray-700">Non-Veg</span>
                            </button>
                        </div>
                    </div>

                    <!-- Category Sections -->
                    <div class="bg-white md:rounded-2xl md:shadow-sm space-y-1 pb-4">
                        @foreach($categories as $category)
                            @if($category->items->count() > 0)
                                <div id="category-{{ $category->id }}" class="scroll-mt-32 md:scroll-mt-28" 
                                    x-show="searchQuery === '' || '{{ strtolower($category->name) }}'.includes(searchQuery.toLowerCase()) || $el.querySelectorAll('.product-item:not([style*=\'none\'])').length > 0">
                                    
                                    <!-- Category Title -->
                                    <div class="p-4 bg-gray-50/50 sticky top-[72px] md:static z-20">
                                        <h3 class="text-lg font-extrabold text-gray-900">{{ $category->name }}</h3>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
                                        @foreach($category->items as $item)
                                            <!-- Grid Item -->
                                            <div class="product-item bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col h-full group"
                                                 x-show="searchQuery === '' || '{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase())">
                                                
                                                <!-- Image Top (Fixed Height) -->
                                                <div class="relative w-full h-48 bg-gray-100 group-hover:opacity-95 transition-opacity">
                                                    @if($item->image_path)
                                                        <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : Storage::url($item->image_path) }}" 
                                                            alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                                                    @endif
                                                    
                                                    <!-- Quick View Button (Restored) -->
                                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 backdrop-blur-[1px]">
                                                         <button type="button" @click='initModal(@json($item, JSON_HEX_APOS))' 
                                                            class="bg-white text-gray-800 text-xs font-bold px-3 py-2 rounded-full shadow-lg hover:bg-gray-50 hover:scale-105 transition-all transform flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                            Quick View
                                                        </button>
                                                    </div>

                                                    <!-- Veg/Non-Veg Badge -->
                                                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded px-1.5 py-1 shadow-sm z-10">
                                                        <div class="border {{ $item->type === 'veg' ? 'border-green-600' : 'border-red-600' }} p-[1px] w-3.5 h-3.5 flex items-center justify-center rounded-[2px]">
                                                            <div class="{{ $item->type === 'veg' ? 'bg-green-600' : 'bg-red-600' }} w-1.5 h-1.5 rounded-full"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Content Bottom -->
                                                <div class="p-3 flex flex-col flex-1 justify-between">
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-sm leading-tight mb-1 line-clamp-2">{{ $item->name }}</h4>
                                                        <p class="text-xs text-gray-500 line-clamp-1 mb-2">{{ $item->description }}</p>
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between mt-auto">
                                                        <span class="text-gray-900 font-bold text-sm">₹{{ $item->price }}</span>
                                                        
                                                        <!-- Add Button (Mini) -->
                                                         <div class="w-20">
                                                             @if(isset(session('cart')[$item->id]))
                                                                <div class="flex items-center justify-between border border-green-500 rounded-md h-7 bg-white shadow-sm">
                                                                    <a href="{{ route('cart.decrease', $item->id) }}" @click="saveScroll()" class="w-6 h-full flex items-center justify-center text-green-600 font-bold hover:bg-green-50 rounded-l-md">-</a>
                                                                    <span class="text-xs font-bold text-green-700">{{ session('cart')[$item->id]['quantity'] }}</span>
                                                                    <a href="{{ route('cart.add', $item) }}" @click="saveScroll()" class="w-6 h-full flex items-center justify-center text-green-600 font-bold hover:bg-green-50 rounded-r-md">+</a>
                                                                </div>
                                                             @else
                                                                @if($item->variations->isEmpty())
                                                                    <form action="{{ route('cart.add', $item) }}" method="GET">
                                                                        <button type="submit" @click="saveScroll()" class="w-full bg-white text-green-600 font-bold text-xs py-1 rounded-md border border-gray-200 shadow-sm uppercase hover:bg-green-50 hover:border-green-200 transition-colors">
                                                                            ADD
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <button type="button" @click='initModal(@json($item, JSON_HEX_APOS))' class="w-full bg-white text-green-600 font-bold text-xs py-1 rounded-md border border-gray-200 shadow-sm uppercase hover:bg-green-50 hover:border-green-200 transition-colors">
                                                                        ADD
                                                                    </button>
                                                                    <div class="hidden text-[8px] text-gray-400 text-center mt-0.5">Customisable</div>
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
                    </div>
                </div>

                <!-- RIGHT COLUMN: Cart (Sticky) -->
                <div class="hidden lg:block lg:col-span-3 sticky top-24">
                     <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900">Your Cart</h2>
                            @if(count(session('cart', [])) > 0)
                                <span class="text-xs text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full">{{ count(session('cart')) }} Items</span>
                            @endif
                        </div>

                        <div class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                            @if(session('cart') && count(session('cart')) > 0)
                                <div class="space-y-4">
                                     @php $total = 0; @endphp
                                     @foreach(session('cart') as $id => $details)
                                        @php $total += $details['price'] * $details['quantity']; @endphp
                                        <div class="flex gap-3">
                                            <!-- Mini Veg/Non-Veg -->
                                             <div class="mt-1">
                                                <div class="border border-green-600 p-[1px] w-3 h-3 flex items-center justify-center bg-white rounded-[2px]">
                                                    <div class="bg-green-600 w-1.5 h-1.5 rounded-full"></div>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start mb-1">
                                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-2 w-3/4">{{ $details['name'] }}</h4>
                                                    <span class="text-sm font-bold text-gray-700">₹{{ $details['price'] * $details['quantity'] }}</span>
                                                </div>
                                                
                                                @if(isset($details['options']))
                                                    <div class="mb-2 space-y-0.5">
                                                        @foreach($details['options'] as $opt)
                                                            <p class="text-[10px] text-gray-500">+ {{ $opt['name'] }}</p>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Mini Qty Control -->
                                                <div class="flex items-center border border-gray-200 rounded w-16 h-6">
                                                    <a href="{{ route('cart.decrease', $id) }}" @click="saveScroll()" class="w-5 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xs">-</a>
                                                    <span class="flex-1 text-center text-xs font-bold text-green-700">{{ $details['quantity'] }}</span>
                                                    <a href="{{ route('cart.increase', $id) }}" @click="saveScroll()" class="w-5 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xs">+</a>
                                                </div>
                                            </div>
                                        </div>
                                     @endforeach
                                </div>

                                <div class="mt-6 pt-4 border-t border-gray-100 space-y-2">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Subtotal</span>
                                        <span>₹{{ $total }}</span>
                                    </div>
                                    <div class="flex justify-between text-base font-bold text-gray-900 pt-2">
                                        <span>To Pay</span>
                                        <span>₹{{ $total }}</span>
                                    </div>
                                    
                                    <a href="{{ route('checkout.index') }}" 
                                        class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-center rounded-xl shadow-lg shadow-green-200 transition-all mt-4">
                                        Checkout
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-900 font-bold mb-1">Your cart is empty</p>
                                    <p class="text-xs text-gray-500">Go ahead and explore our menu</p>
                                </div>
                            @endif
                        </div>
                     </div>
                </div>

            </div>
             <!-- MOBILE FLOATING CART BAR -->
             @if(count(session('cart', [])) > 0)
                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] lg:hidden z-40">
                    <div class="flex justify-between items-center mb-0">
                         @php
                             $itemsCount = 0;
                             $totalMobile = 0;
                             foreach(session('cart') as $details) {
                                 $itemsCount += $details['quantity'];
                                 $totalMobile += $details['price'] * $details['quantity'];
                             }
                         @endphp
                         <div class="flex flex-col">
                             <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ $itemsCount }} Items</span>
                             <span class="text-lg font-extrabold text-gray-900">₹{{ $totalMobile }}</span>
                         </div>
                         <a href="{{ route('checkout.index') }}" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-green-200 flex items-center gap-2">
                             <span>View Cart</span>
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                         </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Flowbite Quick View Modal (Reused) -->
        <div x-show="activeItem" style="display: none;" x-transition.opacity
            class="fixed inset-0 z-50 flex justify-center items-center w-full h-full bg-gray-900/60 backdrop-blur-sm p-4">

            <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden" @click.outside="activeItem = null">
                
                <!-- Close button -->
                <button @click="activeItem = null" type="button"
                    class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center bg-black/10 hover:bg-black/20 text-white rounded-full transition-colors backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Scrollable Body -->
                <div class="overflow-y-auto flex-1 custom-scrollbar">
                     <!-- Image Header -->
                    <div class="relative h-64 w-full bg-gray-100">
                         <template x-if="activeItem?.image_path">
                            <img :src="activeItem.image_path.startsWith('http') ? activeItem.image_path : '/storage/' + activeItem.image_path"
                                :alt="activeItem.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!activeItem?.image_path">
                            <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                        </template>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                             <div>
                                <div class="border border-green-600 p-[1px] w-4 h-4 flex items-center justify-center bg-white rounded-[2px] mb-2 inline-flex">
                                    <div class="bg-green-600 w-2 h-2 rounded-full"></div>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900" x-text="activeItem?.name"></h3>
                             </div>
                             <span class="text-xl font-bold text-gray-900" x-text="'₹' + activeItem?.price"></span>
                        </div>
                        
                        <p class="text-gray-500 text-sm mb-6 leading-relaxed" x-text="activeItem?.description"></p>
                        
                        <hr class="border-gray-100 mb-6">

                        <form :action="'/cart/add/' + activeItem?.id" method="GET" id="add-to-cart-form">
                            <!-- Variations -->
                            <template x-if="activeItem?.variations && activeItem.variations.length > 0">
                                <div class="space-y-6 mb-8">
                                    <template x-for="variation in activeItem.variations" :key="variation.id">
                                        <div>
                                            <h4 class="font-bold text-gray-900 mb-3 text-base flex items-center gap-2">
                                                <span x-text="variation.name"></span>
                                                <span x-show="variation.required" class="text-red-500 text-xs">*</span>
                                            </h4>

                                            <!-- Options Loop (Simplified for brevity, same logic as before) -->
                                             <div class="space-y-3">
                                                <template x-for="option in variation.options" :key="option.id">
                                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-green-500 transition-colors cursor-pointer" @click="variation.type === 'radio' ? document.getElementById('option-' + option.id).click() : ''">
                                                        <div class="flex items-center">
                                                            <input :id="'option-' + option.id" :type="variation.type"
                                                                :name="'options[' + variation.id + ']' + (variation.type === 'checkbox' ? '[]' : '')"
                                                                :value="option.id"
                                                                class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                                                :data-price="option.price" @change="updatePrice()">
                                                            <label :for="'option-' + option.id"
                                                                class="ml-3 text-sm font-medium text-gray-900 cursor-pointer">
                                                                <span x-text="option.name"></span>
                                                            </label>
                                                        </div>
                                                        <span class="text-sm text-gray-500" x-show="Number(option.price) > 0" x-text="'+ ₹' + option.price"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </form>
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div class="p-4 bg-white border-t border-gray-100 rounded-b-2xl">
                     <button type="submit" form="add-to-cart-form" @click="saveScroll()"
                        class="w-full flex justify-between items-center text-white bg-green-600 hover:bg-green-700 font-bold rounded-xl text-lg px-6 py-3.5 shadow-lg transition-all active:scale-[0.98]">
                        <span>Add Item</span>
                        <span x-text="'₹' + currentPrice"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>