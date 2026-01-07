<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;

class BackLink
{
    /**
     * Build a safe "back" URL.
     *
     * - Uses URL::previous() when available.
     * - Falls back to given route/url when previous is empty or equals current.
     */
    public static function url(string $fallbackUrl): string
    {
        try {
            $prev = URL::previous();
            $current = URL::current();

            // Persist last visited URL (not including current) to break back-loops.
            // Example loop:
            // 1) settings/user -> 2) users/create -> back -> 3) settings/user
            // Now on (3), URL::previous() will often be (2) again, which would loop.
            // If previous equals our last visited URL, we ignore it and use fallback.
            $lastVisited = session()->get('_last_visited_url');
            session()->put('_last_visited_url', $current);

            if (
                is_string($prev)
                && $prev !== ''
                && $prev !== $current
                && (!is_string($lastVisited) || $prev !== $lastVisited)
                && self::isSafeUrl($prev)
            ) {
                return $prev;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $fallbackUrl;
    }

    private static function isSafeUrl(string $url): bool
    {
        // Only allow same-origin URLs.
        $appUrl = config('app.url');
        if (is_string($appUrl) && $appUrl !== '' && str_starts_with($url, $appUrl)) {
            return true;
        }

        // If app.url isn't set properly, still allow relative URLs.
        return str_starts_with($url, '/');
    }
}
