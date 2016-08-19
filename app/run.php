<?php
use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\UseCase\WarmUpCacheForSitePresenter;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSite;

require_once __DIR__ . '/../vendor/autoload.php';

class EchoingWarmUpCacheForSitePresenter implements WarmUpCacheForSitePresenter
{
    public function present($url)
    {
        echo "$url\n";
    }
}

$run = function () {
    $retriever = new FileGetContentsUrlRetriever;
    $useCase = new WarmUpCacheForSite(new GetUrlsFromSiteMap($retriever), new WarmCacheOfOneUrl($retriever));

    $sitemaps = require __DIR__ . '/config.php';

    foreach ($sitemaps as $sitemap) {
        $useCase->warmUpSiteCache($sitemap, new EchoingWarmUpCacheForSitePresenter);
    }
};
$run();
