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
        $tags = Tag::where('workspace_id', Auth::user()->currentWorkspace->id)->get();

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

        $workspace = Auth::user()->currentWorkspace;

        $label = new Tag;
        $label->workspace_id = $workspace->id;
        $label->name = $request->name;
        $label->color = $request->color;
        $label->sort = Tag::where('workspace_id', $workspace->id)->count() + 1;
        $label->save();

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

        $tag = Tag::where('id', $id)->where('workspace_id', Auth::user()->currentWorkspace->id)->firstOrFail();
        $tag->name = $request->name;
        $tag->color = $request->color;
        $tag->save();

        session()->flash('flash.banner', 'Tag updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $tag = Tag::where('workspace_id', Auth::user()->currentWorkspace->id)->where('id', $id)->firstOrFail();
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

        $workspace = Auth::user()->currentWorkspace;

        DB::beginTransaction();

        try {
            foreach ($request->tags as $sort => $tag) {
                $tag = Tag::where('id', $tag['id'])->where('workspace_id', $workspace->id)->firstOrFail();
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
