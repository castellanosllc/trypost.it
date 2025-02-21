<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;

use App\Enums\PostContent\Status;
use App\Models\Account;
use App\Models\Post;
use App\Models\PostContent;

class PostContentController extends Controller
{
    public function store($id, Request $request)
    {
        $request->validate([
            'account_id' => 'required',
        ]);

        $user = Auth::user();

        $account = Account::where('space_id', $user->currentSpace->id)->where('id', $request->account_id)->firstOrFail();
        $post = Post::where('space_id', $user->currentSpace->id)->where('id', $id)->firstOrFail();

        // create or update
        PostContent::create([
            'content' => $request->content,
            'status' => Status::DRAFT,
            'account_id' => $account->id,
            'post_id' => $post->id,
            'platform' => $account->platform,
        ]);

        return back();
    }

    public function update($id, $postContentId, Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $user = Auth::user();

        $post = Post::where('space_id', $user->currentSpace->id)->where('id', $id)->firstOrFail();
        $postContent = PostContent::where('post_id', $post->id)->where('id', $postContentId)->firstOrFail();

        $postContent->update([
            'content' => $request->content,
        ]);

        return back();
    }

    public function destroy($id, $postContentId)
    {
        $user = Auth::user();

        $post = Post::where('space_id', $user->currentSpace->id)->where('id', $id)->firstOrFail();

        $postContent = PostContent::where('post_id', $post->id)->where('id', $postContentId)->firstOrFail();
        $postContent->delete();

        return back();
    }
}
