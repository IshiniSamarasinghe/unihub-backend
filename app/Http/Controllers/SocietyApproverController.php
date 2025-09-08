<?php

// app/Http/Controllers/SocietyApproverController.php
// app/Http/Controllers/SocietyApproverController.php
namespace App\Http\Controllers;

use App\Models\SocietyApprover;
use Illuminate\Http\Request;

class SocietyApproverController extends Controller
{
    public function index()
    {
        return response()->json(
            SocietyApprover::select('id','society','position','whatsapp_number','email')
                ->orderBy('id')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'society' => 'required|string',
            'position' => 'required|string',
            'whatsapp_number' => 'required|string',
            'email' => 'nullable|email',
        ]);

        $row = SocietyApprover::create($data);
        return response()->json($row, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'society' => 'sometimes|string',
            'position' => 'sometimes|string',
            'whatsapp_number' => 'sometimes|string',
            'email' => 'nullable|email',
        ]);

        $row = SocietyApprover::findOrFail($id);
        $row->update($data);
        return response()->json($row);
    }

    public function destroy($id)
    {
        SocietyApprover::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }
}
