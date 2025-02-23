<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Media;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'media' => ['required', 'file'],
            'collection' => 'required',
            'model' => 'required',
            'model_id' => 'required',
            'visibility' => 'required|in:public,private',
        ]);

        $model = 'App?Models?'.$request->model;
        $model = str_replace('?', '\\', $model);

        $model = $model::where('id', $request->model_id)->firstOrFail();
        $upload = $model->addMediaFromRequest('media')
            ->addCustomHeaders([
                'ACL' => $request->visibility === 'public' ? 'public-read' : 'private',
            ])
            ->toMediaCollection($request->collection);

        return response()->json($upload);
    }

    // copy media to another model
    public function copy(Media $media): void
    {
        // replicate files
        foreach ($media->getMedia('files') as $media) {
            $media->copy(
                $media,
                'files'
            );
        }
    }

    public function download($id, Request $request)
    {
        $media = Media::where('id', $id)->firstOrFail();
        return Storage::download($media->getPath());
    }

    public function destroy($modelId, $id)
    {
        $media = Media::where('id', $id)
            ->where('model_id', $modelId)
            ->firstOrFail();

        $media->delete();

        return back();
    }
}
