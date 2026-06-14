<?php

final class Redirector
{
    public static function to(string $url): never
    {
        $base = rtrim(env('APP_URL', ''), '/');
        $fullUrl = str_starts_with($url, '/') ? $base . $url : $url;
        header("Location: {$fullUrl}");
        exit;
    }

}
