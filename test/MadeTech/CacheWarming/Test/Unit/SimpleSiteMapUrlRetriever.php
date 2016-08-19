<?php
namespace MadeTech\CacheWarming\Test\Unit;

use MadeTech\CacheWarming\UrlRetriever;

class SimpleSiteMapUrlRetriever implements UrlRetriever
{
    const LOCATION = 'https://example.com/a/url/path/here/';

    public function get($url)
    {
        $location = self::LOCATION;
        return /** @lang XML */
            <<<SITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{$location}</loc>
    </url>
</urlset>
SITEMAP;

    }
}
