<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventMedia;
use Illuminate\Support\Facades\Storage;

class EventMediaController extends Controller
{
    public function upload(Request $request)
{
    try {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'images.*' => 'nullable|file|mimes:jpeg,jpg,png|max:5120',
            'videos.*' => 'nullable|file|mimes:mp4,avi,mov|max:20480',
        ]);

        $media = [];

        // Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = $file->store('event_media', 'public');

                $media[] = EventMedia::create([
                    'event_id' => $request->event_id,
                    'file_path' => $filename,
                    'type' => 'image'
                ]);
            }
        }

        // Videos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                $filename = $file->store('event_media', 'public');

                $media[] = EventMedia::create([
                    'event_id' => $request->event_id,
                    'file_path' => $filename,
                    'type' => 'video'
                ]);
            }
        }

        return response()->json(['message' => 'Media uploaded', 'media' => $media]);
    } catch (\Exception $e) {
        \Log::error('Upload failed: ' . $e->getMessage());
        return response()->json(['error' => 'Upload failed', 'details' => $e->getMessage()], 500);
    }
}
public function forEvent($eventId)
{
    $media = EventMedia::where('event_id', $eventId)->get();

    // Add full URL for frontend rendering
    $media->transform(function ($item) {
        $item->url = asset('storage/' . $item->file_path);
        return $item;
    });

    return response()->json($media);
}

}
