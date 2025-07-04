<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreItem;

class StoreItemController extends Controller
{
    public function index()
    {
        return StoreItem::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'faculty' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|string',
            'details' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('store_items', 'public');
            $validated['image_path'] = $path;
        }

        $storeItem = StoreItem::create($validated);

        return response()->json($storeItem, 201);
    }

    public function destroy($id)
{
    $item = StoreItem::findOrFail($id);

    // Delete the image from storage if exists
    if ($item->image_path && \Storage::disk('public')->exists($item->image_path)) {
        \Storage::disk('public')->delete($item->image_path);
    }

    $item->delete();

    return response()->json(['message' => 'Item deleted successfully']);
}


public function update(Request $request, $id)
{
    $storeItem = StoreItem::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required|string',
        'faculty' => 'required|string',
        'description' => 'required|string',
        'price' => 'required|string',
        'details' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('image')) {
        // Delete old image
        if ($storeItem->image_path && \Storage::disk('public')->exists($storeItem->image_path)) {
            \Storage::disk('public')->delete($storeItem->image_path);
        }
        $path = $request->file('image')->store('store_items', 'public');
        $validated['image_path'] = $path;
    }

    $storeItem->update($validated);

    return response()->json($storeItem);
}


}
