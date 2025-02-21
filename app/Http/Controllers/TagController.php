<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Controllers\Controller;

use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tags = Tag::where('space_id', $user->currentSpace->id)->get();

        return Inertia::render('Tag/Index', [
            'tags' => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'color' => ['required', 'max:255'],
        ]);

        $space = Auth::user()->currentSpace;

        Tag::create([
            'space_id' => $space->id,
            'workspace_id' => $space->workspace_id,
            'name' => $request->name,
            'color' => $request->color,
            'sort' => Tag::where('space_id', $space->id)->count() + 1,
        ]);

        session()->flash('flash.banner', 'Tag created successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'color' => ['required', 'max:255'],
        ]);

        $space = Auth::user()->currentSpace;

        $tag = Tag::where('id', $id)->where('space_id', $space->id)->firstOrFail();
        $tag->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        session()->flash('flash.banner', 'Tag updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $space = Auth::user()->currentSpace;

        $tag = Tag::where('id', $id)->where('space_id', $space->id)->firstOrFail();
        $tag->delete();

        session()->flash('flash.banner', 'Tag deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function sort(Request $request)
    {
        $request->validate([
            'tags' => ['required', 'array']
        ]);

        $space = Auth::user()->currentSpace;

        DB::beginTransaction();

        try {
            foreach ($request->tags as $sort => $tag) {
                $tag = Tag::where('id', $tag['id'])->where('space_id', $space->id)->firstOrFail();
                $tag->sort = $sort + 1;
                $tag->save();
            }

            DB::commit();
            return response()->json();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['status' => 'error', 'message' => 'Could not sort update, please try again.'], 500);
        }
    }
}
