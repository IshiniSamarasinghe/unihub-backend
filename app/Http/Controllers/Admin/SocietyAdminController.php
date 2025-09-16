<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;

class SocietyAdminController extends Controller
{
    // optional: quick list for an admin-only grid
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        return Society::orderBy('name')->get();
    }

    // ✅ NEW: Admin Blade page (mirrors Users table style)
    public function page(Request $request)
    {
        $this->authorizeAdmin($request);

        $societies = Society::with(['university:id,name'])
            ->orderBy('name')
            ->get();

        return view('admin.societies.index', compact('societies'));
    }

    public function update(Request $request, Society $society)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name'                     => 'sometimes|string|max:255',
            'slug'                     => 'sometimes|string|max:255',
            'description'              => 'nullable|string',
            'join_link'                => 'nullable|string|max:2048',
            'logo_url'                 => 'nullable|string|max:2048',
            'registration_open_date'   => 'nullable|date',
            'registration_close_date'  => 'nullable|date|after_or_equal:registration_open_date',
            'university_id'            => 'nullable|integer|exists:universities,id',
        ]);

        $society->fill($data)->save();

        return response()->json($society->fresh(), 200);
    }

    // ✅ NEW: create society (for admin panel “Add Society”)
    public function store(Request $request)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'slug'                     => 'required|string|max:255|unique:societies,slug',
            'description'              => 'nullable|string',
            'join_link'                => 'nullable|string|max:2048',
            'logo_url'                 => 'nullable|string|max:2048',
            'registration_open_date'   => 'nullable|date',
            'registration_close_date'  => 'nullable|date|after_or_equal:registration_open_date',
            'university_id'            => 'nullable|integer|exists:universities,id',
        ]);

        $society = Society::create($data);

        return response()->json($society, 201);
    }

    // ✅ NEW: delete society
    public function destroy(Request $request, Society $society)
    {
        $this->authorizeAdmin($request);

        $society->delete();

        return response()->json(['message' => 'Society deleted successfully.'], 200);
    }

    private function authorizeAdmin(Request $request): void
    {
        // Adjust to your field; earlier you used user_type === 'admin'
        if (!in_array(($request->user()->user_type ?? null), ['admin','super_user'], true)) {
            abort(403, 'Forbidden');
        }
    }
}
