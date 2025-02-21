<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Controllers\Controller;

use App\Models\Hashtag;

class HashtagController extends Controller
{
    public function index()
    {
        $hashtags = Hashtag::where('workspace_id', Auth::user()->currentWorkspace->id)->get();

        return Inertia::render('Hashtag/Index', [
            'hashtags' => $hashtags,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'collection' => ['required', 'string'],
        ]);

        $workspace = Auth::user()->currentWorkspace;

        Hashtag::create([
            'workspace_id' => $workspace->id,
            'name' => $request->name,
            'collection' => $request->collection,
        ]);

        session()->flash('flash.banner', 'Tag created successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'collection' => ['required', 'string'],
        ]);

        $hashtag = Hashtag::where('id', $id)->where('workspace_id', Auth::user()->currentWorkspace->id)->firstOrFail();
        $hashtag->update([
            'name' => $request->name,
            'collection' => $request->collection,
        ]);

        session()->flash('flash.banner', 'Hashtag updated successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function destroy($id)
    {
        $hashtag = Hashtag::where('workspace_id', Auth::user()->currentWorkspace->id)->where('id', $id)->firstOrFail();
        $hashtag->delete();

        session()->flash('flash.banner', 'Hashtag deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
