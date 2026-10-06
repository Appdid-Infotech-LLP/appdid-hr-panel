<?php

namespace App\Support;

class Url
{
    /**
     * Resumes and hand-typed fields often give "linkedin.com/in/jane" or
     * "www.jane.dev" without a scheme, which isn't a usable link (it'd be
     * treated as relative) and fails the `url` rule. Prefix https:// unless a
     * scheme is already there; blank input becomes null.
     */
    public static function withScheme(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            return $url;
        }

        return 'https://'.ltrim($url, '/');
    }
}
