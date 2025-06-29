<?php

namespace App\Http\Controllers;

use App\Models\SocietyApprover;
use Illuminate\Http\Request;

class SocietyApproverController extends Controller
{
    public function index()
    {
        return SocietyApprover::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'society' => 'required|string',
            'position' => 'required|string',
            'whatsapp_number' => 'required|string',
            'email' => 'nullable|email',
        ]);

        return SocietyApprover::create($validated);
    }

    public function update(Request $request, $id)
    {
        $approver = SocietyApprover::findOrFail($id);
        $approver->update($request->all());
        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        SocietyApprover::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
