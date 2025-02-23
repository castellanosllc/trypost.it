<?php

if (!function_exists('email_asset')) {
    /**
     * Generate an absolute URL for email assets.
     *
     * @param  string  $path
     * @return string
     */
    function email_asset($path)
    {
        $base_url = config('app.email_asset_url', config('app.url'));
        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }
}
