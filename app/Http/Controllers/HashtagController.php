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
        $space = Auth::user()->currentSpace;

        $hashtags = Hashtag::where('space_id', $space->id)
            ->orderBy('created_at', 'asc')
            ->get();

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

        $space = Auth::user()->currentSpace;

        Hashtag::create([
            'space_id' => $space->id,
            'workspace_id' => $space->workspace_id,
            'name' => $request->name,
            'collection' => $request->collection,
        ]);

        session()->flash('flash.banner', 'Hashtag created successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'collection' => ['required', 'string'],
        ]);

        $space = Auth::user()->currentSpace;

        $hashtag = Hashtag::where('id', $id)->where('space_id', $space->id)->firstOrFail();
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
        $space = Auth::user()->currentSpace;

        $hashtag = Hashtag::where('space_id', $space->id)->where('id', $id)->firstOrFail();
        $hashtag->delete();

        session()->flash('flash.banner', 'Hashtag deleted successful.');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }
}
