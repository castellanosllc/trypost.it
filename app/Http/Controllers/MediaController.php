<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\ContentRangeUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Media;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
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

    public function chunk(Request $request)
    {
        // Initialize file receiver for chunked uploads
        $receiver = new FileReceiver(
            UploadedFile::fake()->createWithContent('file', $request->getContent()),
            $request,
            ContentRangeUploadHandler::class
        );

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        // If upload is completed, call store() with the final file
        if ($save->isFinished()) {
            $upload = $this->storeChunk($request, $save->getFile());
        }

        // Continue receiving chunks
        $save->handler();

        return response()->json($upload);
    }

    public function storeChunk(Request $request, UploadedFile $file)
    {
        $request->validate([
            'collection' => 'required',
            'model' => 'required',
            'model_id' => 'required',
            'visibility' => 'required|in:public,private',
        ]);

        $model = 'App?Models?' . $request->model;
        $model = str_replace('?', '\\', $model);

        $model = $model::where('id', $request->model_id)->firstOrFail();
        $upload = $model->addMedia($file)
            ->addCustomHeaders([
                'ACL' => $request->visibility === 'public' ? 'public-read' : 'private',
            ])
            ->toMediaCollection($request->collection);

        return $upload;
    }

    // copy media to another model
    public function copy(Request $request)
    {
        $request->validate([
            'media_id' => ['required', 'exists:medias,id'],
            'model' => ['required'],
            'model_id' => ['required'],
            'collection' => ['required'],
        ]);

        // get media
        $media = Media::where('id', $request->media_id)->firstOrFail();

        // get model
        $model = 'App?Models?'.$request->model;
        $model = str_replace('?', '\\', $model);

        $model = $model::where('id', $request->model_id)->firstOrFail();

        // copy media to model
        $copy = $media->copy($model, $request->collection);

        return response()->json($copy);
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
