<?php

namespace App\Support;

/**
 * URL path segments reserved by static routes in routes/web.php.
 * CMS page slugs must not collide with these.
 */
class ReservedPageSlugs
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            'estimate',
            'portal',
            'dashboard',
            'admin',
            'demo',
            'proposals',
            'invoices',
            'api',
            'robots.txt',
            'sitemap.xml',
        ];
    }
}
