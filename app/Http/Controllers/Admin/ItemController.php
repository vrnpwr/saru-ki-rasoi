<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('category')->get();
        return view('admin.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', // 2MB Max
            'variations' => 'nullable|array',
            'variations.*.name' => 'required|string',
            'variations.*.type' => 'required|in:radio,checkbox,select',
            'variations.*.required' => 'boolean',
            'variations.*.options' => 'nullable|array',
            'variations.*.options.*.name' => 'required|string',
            'variations.*.options.*.price' => 'required|numeric|min:0',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $data['image_path'] = $path;
        }

        $data['name'] = $request->name; // Explicit assignment if not in $request->except
        // Actually $data is from $request->except('image') which includes 'variations'. 
        // We should exclude variations from Item creation data.
        $itemData = \Illuminate\Support\Arr::except($data, ['variations']);
        $item = Item::create($itemData);

        if ($request->has('variations')) {
            foreach ($request->variations as $varData) {
                // Ensure required is boolean, checkboxes might send '1' or 'on' or nothing if not checked in some forms, 
                // but validation expects boolean.
                // Assuming JSON payload or proper form handling.
                $variation = $item->variations()->create([
                    'name' => $varData['name'],
                    'type' => $varData['type'],
                    'required' => filter_var($varData['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);

                if (isset($varData['options'])) {
                    foreach ($varData['options'] as $optData) {
                        $variation->options()->create([
                            'name' => $optData['name'],
                            'price' => $optData['price'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required|string',
            'variations.*.type' => 'required|in:radio,checkbox,select',
            'variations.*.required' => 'boolean',
            'variations.*.options' => 'nullable|array',
            'variations.*.options.*.name' => 'required|string',
            'variations.*.options.*.price' => 'required|numeric|min:0',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $path = $request->file('image')->store('items', 'public');
            $data['image_path'] = $path;
        }

        $itemData = \Illuminate\Support\Arr::except($data, ['variations']);
        $item->update($itemData);

        // Update Variations
        // Simplest strategy: Delete all and recreate.
        // Be careful: this changes IDs.
        $item->variations()->each(function ($variation) {
            $variation->delete(); // This ensures cascading if handled by Model, but standard DB cascade is better.
        });
        // Or better:
        // $item->variations()->delete(); // Depends on DB cascade for options.

        if ($request->has('variations')) {
            foreach ($request->variations as $varData) {
                $variation = $item->variations()->create([
                    'name' => $varData['name'],
                    'type' => $varData['type'],
                    'required' => filter_var($varData['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);

                if (isset($varData['options'])) {
                    foreach ($varData['options'] as $optData) {
                        $variation->options()->create([
                            'name' => $optData['name'],
                            'price' => $optData['price'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        return redirect()->route('admin.items.index')->with('success', 'Item deleted successfully.');
    }
}
