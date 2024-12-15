<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Niche;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alt_text' => 'required|string|max:255',
            'niche_id' => 'required|exists:niche_static_contents,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|string|max:255' 
        ]);

        // Handle the image file upload
        $path = $request->file('image')->store('images', 'public');

        $image = Image::create([
            'name' => $validated['name'],
            'alt_text' => $validated['alt_text'],
            'filename' => $path,
            'niche_id' => $validated['niche_id'],
            'title' => $validated['title'],

        ]);
        \Log::info('Upload request received', $request->all());

        return response()->json($image, 201);
    }

    public function indexByNiche($nicheId)
    {
        $images = Image::where('niche_id', $nicheId)->get();
        return response()->json($images);
    }

    public function show(Image $image)
    {
        return response()->json($image);
    }

    public function update(Request $request, Image $image)
    {
        if ($request->has('alt_text')) {
            $image->alt_text = $request->alt_text;
        }
        if ($request->has('title')) {
            $image->title = $request->title;
        }

        $image->save();

        return response()->json($image);
    }
}
