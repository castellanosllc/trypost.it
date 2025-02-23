<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


if (!function_exists('uploadFromUrl')) {
    /**
     * Generate an absolute URL for email assets.
     *
     * @param  string  $path
     * @return string
     */
    function uploadFromUrl(string $url, string $folder): ?string
    {
        try {
            // Get the file contents from the URL
            $response = Http::get($url);

            if ($response->failed()) {
                return null;
            }

            // Get file extension from URL or default to .jpg
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';

            // Generate a unique filename
            $filename = $folder . '/' . Str::random(40) . '.' . $extension;

            // Store the file in the specified disk
            Storage::put($filename, $response->body());

            // Ensure the file is publicly accessible
            Storage::setVisibility($filename, 'public');

            return $filename; // Return the relative path instead of the full URL
        } catch (\Exception $e) {
            return null;
        }
    }
}
