<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;

use App\Enums\Post\Status;

use App\Models\Account;
use App\Models\Post;
use App\Models\PostStat;

use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $workspace = Auth::user()->currentWorkspace;

        if ($request->has('start') && $request->has('end')) {
            $query = Post::where('workspace_id', $workspace->id)->latest();
            $query->whereBetween('scheduled_at', [$request->start, $request->end]);
            $posts = $query->get();

            return response()->json($posts);
        }
        return Inertia::render('Post/Index/Index', [
            'accounts' => $id ? Account::where('workspace_id', $workspace->id)->get() : [],
            'post' => $id ? Post::where('workspace_id', $workspace->id)
                ->with('postStats')
                ->where('id', $id)
                ->firstOrFail() : null,
        ]);
    }

    public function store(CreateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $post = Post::create([
            'workspace_id' => $workspace->id,
            'content' => '',
            'status' => Status::DRAFT,
            'scheduled_at' => $request->scheduled_at ?? now(),
        ]);

        return redirect(route('posts.index', ['id' => $post->id]));
    }

    public function update($id, UpdateRequest $request)
    {
        $workspace = Auth::user()->currentWorkspace;

        $post = Post::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        $post->update([
            'content' => $request->content,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        // delete all post stats
        PostStat::where('post_id', $post->id)->delete();

        foreach ($request->accounts as $accountId) {

            $account = Account::where('workspace_id', $workspace->id)->where('id', $accountId)->firstOrFail();

            // create or update
            PostStat::create([
                'account_id' => $account->id,
                'post_id' => $post->id,
                'platform' => $account->platform,
            ]);
        }

        return redirect(route('posts.index'));
    }

    public function destroy($id)
    {
        $workspace = Auth::user()->currentWorkspace;

        $post = Post::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        $post->delete();

        session()->flash('flash.banner', 'Post deleted successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('posts.index'));
    }
}
