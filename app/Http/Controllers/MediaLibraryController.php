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

class MediaLibraryController extends Controller
{
    public function index(): Response
    {

        $medias = Auth::user()->currentSpace->getMedia('media-library');

        return Inertia::render('MediaLibrary/Index', [
            'medias' => $medias,
        ]);
    }
}
