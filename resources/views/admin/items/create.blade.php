<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                            <select name="category_id" id="category_id"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Item Name</label>
                            <input type="text" name="name" id="name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description"
                                class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                            @error('description') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                            <input type="number" step="0.01" name="price" id="price"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                            @error('price') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                            <input type="file" name="image" id="image"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('image') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Variations Section -->
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-bold mb-2">Variations (e.g. Size, Toppings)</h3>
                            <div id="variations-wrapper" class="space-y-4">
                                <!-- Variations added here -->
                            </div>
                            <button type="button" onclick="addVariation()"
                                class="mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded text-sm">
                                + Add Variation
                            </button>
                        </div>

                        <script>
                            let variationCount = 0;

                            function addVariation() {
                                const wrapper = document.getElementById('variations-wrapper');
                                const index = variationCount++;
                                const html = `
                                    <div class="border rounded p-4 bg-gray-50 relative" id="variation-${index}">
                                        <button type="button" onclick="removeVariation(${index})" class="absolute top-2 right-2 text-red-500 font-bold">&times;</button>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                                            <div>
                                                <label class="block text-xs font-bold mb-1">Variation Name</label>
                                                <input type="text" name="variations[${index}][name]" placeholder="e.g. Size" class="shadow border rounded w-full py-1 px-2 text-sm" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold mb-1">Type</label>
                                                <select name="variations[${index}][type]" class="shadow border rounded w-full py-1 px-2 text-sm">
                                                    <option value="radio">Single Selection (Radio)</option>
                                                    <option value="checkbox">Multiple Selection (Checkbox)</option>
                                                    <option value="select">Dropdown</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center mt-6">
                                                <input type="hidden" name="variations[${index}][required]" value="0">
                                                <input type="checkbox" name="variations[${index}][required]" value="1" class="mr-2">
                                                <label class="text-xs font-bold">Required?</label>
                                            </div>
                                        </div>
                                        
                                        <div class="ml-4 border-l-2 border-gray-300 pl-4 mt-2">
                                            <label class="block text-xs font-bold mb-1 text-gray-600">Options</label>
                                            <div id="options-wrapper-${index}" class="space-y-2"></div>
                                            <button type="button" onclick="addOption(${index})" class="mt-2 text-blue-500 text-xs font-bold hover:underline">+ Add Option</button>
                                        </div>
                                    </div>
                                `;
                                wrapper.insertAdjacentHTML('beforeend', html);
                                // Add one default option row
                                addOption(index);
                            }

                            function removeVariation(index) {
                                document.getElementById(`variation-${index}`).remove();
                            }

                            function addOption(variationIndex) {
                                const wrapper = document.getElementById(`options-wrapper-${variationIndex}`);
                                const count = wrapper.children.length; // Simple counter based on children
                                const html = `
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="variations[${variationIndex}][options][${count}][name]" placeholder="Option Name (e.g. Small)" class="shadow border rounded w-1/2 py-1 px-2 text-sm" required>
                                        <input type="number" step="0.01" name="variations[${variationIndex}][options][${count}][price]" placeholder="Price (+0.00)" class="shadow border rounded w-1/3 py-1 px-2 text-sm" required>
                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold ml-2">&times;</button>
                                    </div>
                                `;
                                wrapper.insertAdjacentHTML('beforeend', html);
                            }
                        </script>

                        <div class="flex items-center justify-between">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit">
                                Create Item
                            </button>
                            <a href="{{ route('admin.items.index') }}"
                                class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>