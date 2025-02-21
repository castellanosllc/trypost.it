<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;

use App\Enums\User\Role;

use App\Models\Space;
use App\Models\Plan;

class SpaceController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Space/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $space = Space::create([
                'name' => $request->name,
                'workspace_id' => $user->workspace_id,
            ]);

            $user->update([
                'current_space_id' => $space->id,
            ]);

            DB::commit();

            return redirect(route('posts.index'));
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            session()->flash('flash.banner', 'Error creating space');
            session()->flash('flash.bannerStyle', 'danger');
            return back();
        }
    }

    public function setCurrentStore(Request $request)
    {
        $space = Space::findOrFail($request->space_id);

        // check if the space belongs to the user's workspace
        if ($space->workspace_id !== $request->user()->workspace_id) {
            abort(403);
        }

        $request->user()->update([
            'current_space_id' => $space->id,
        ]);

        return Inertia::location(route('posts.index'));
    }
}
