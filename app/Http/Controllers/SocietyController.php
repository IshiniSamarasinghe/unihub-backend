<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocietyController extends Controller
{
    /**
     * Public list of societies
     * Adds: university_name (if relation exists)
     * Adds: can_edit flag (true only if user has a super role in that society)
     */
    public function index(Request $request)
    {
        $user = $request->user(); // null if guest
        $societies = Society::with('university:id,name')->orderBy('name')->get();

        $superRoles = [
            'secretary',
            'junior treasurer','juniortreasurer','junior_treasurer',
            'coeditor','co-editor','co_editor',
            'editor',
            'vice secretary','vicesecretary','vice_secretary',
            'social media coordinator','socialmediacoordinator','social_media_coordinator',
            'committeemember','committee member','committee_member',
        ];

        return $societies->map(function ($s) use ($user, $superRoles) {
            if (!$user) {
                $s->can_edit = false;
                return $s;
            }

            // build the society key (ex: "itsa-society")
            $socKey = strtolower(($s->slug ?? '') . '-society');

            $s->can_edit = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->whereRaw('LOWER(society) = ?', [$socKey])
                ->whereIn('role', $superRoles)
                ->exists();

            return $s;
        });
    }

    /**
     * Update society (super user OR admin only)
     */
    public function update(Request $request, Society $society)
    {
        $user = $request->user();

        if (($user->user_type ?? null) !== 'admin') {
            $superRoles = [
                'secretary',
                'junior treasurer','juniortreasurer','junior_treasurer',
                'coeditor','co-editor','co_editor',
                'editor',
                'vice secretary','vicesecretary','vice_secretary',
                'social media coordinator','socialmediacoordinator','social_media_coordinator',
                'committeemember','committee member','committee_member',
            ];

            $societyKey = strtolower(($society->slug ?? '') . '-society');

            $allowed = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->whereRaw('LOWER(society) = ?', [$societyKey])
                ->whereIn('role', $superRoles)
                ->exists();

            if (!$allowed) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        // if allowed, validate + save
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'slug'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'join_link'   => 'nullable|string|max:2048',
            'logo_url'    => 'nullable|string|max:2048',
        ]);

        $society->fill($data)->save();

        return response()->json($society->fresh(), 200);
    }
}
