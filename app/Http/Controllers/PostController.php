<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;

use App\Enums\PostStat\Status as PostStatStatus;

use App\Models\Account;
use App\Models\Post;
use App\Models\PostStat;

use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        if ($request->has('start') && $request->has('end')) {
            $query = Post::where('workspace_id', $workspace->id)->latest();
            $query->whereBetween('scheduled_at', [$request->start, $request->end]);
            $posts = $query->get();

            return response()->json($posts);
        }

        $accounts = Account::where('workspace_id', $workspace->id)->get();

        return Inertia::render('Post/Index/Index', [
            'accounts' => $accounts,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        // $response = Gate::inspect('reached-link-limit', $workspace);
        // if (!$response->allowed()) {
        //     session()->flash('flash.banner', 'You have reached the limit of links, please upgrade your plan.');
        //     session()->flash('flash.bannerStyle', 'danger');
        //     return back();
        // }

        $post = Post::create([
            'workspace_id' => $workspace->id,
            'content' => $request->content,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        foreach ($request->accounts as $accountId) {

            $account = Account::where('workspace_id', $workspace->id)->where('id', $accountId)->firstOrFail();

            PostStat::create([
                'account_id' => $account->id,
                'post_id' => $post->id,
                'platform' => $account->platform,
            ]);
        }

        session()->flash('flash.banner', 'Post created successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('posts.index'));
    }

    public function update($id, UpdateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $post = Post::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        $post->update([
            'content' => $request->content,
        ]);

        session()->flash('flash.banner', 'Link updated successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('posts.index'));
    }

    public function destroy($id, Request $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $link = Link::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        $link->delete();

        session()->flash('flash.banner', 'Link deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('posts.index'));
    }
}
