<?php
use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config\ArrayConfigProvider;
use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\UseCase\CacheWarmerPresenter;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSiteMap;

require_once __DIR__ . '/../vendor/autoload.php';

class EchoingCacheWarmerPresenter implements CacheWarmerPresenter
{
    private $siteMapCount;
    /** @var int */
    private $count;

    /** @var int[] */
    private $urls;

    /** @var string */
    private $siteMapUrl;

    /** @var int[] */
    private $siteMaps;

    public function presentVisitedUrl($url)
    {
        $currentUrlIndex = $this->urls[$url] + 1;
        $currentSiteMapIndex = $this->siteMaps[$this->siteMapUrl] + 1;
        echo "$currentUrlIndex/{$this->count}, (Sitemap $currentSiteMapIndex/{$this->siteMapCount}): $url\n";
    }

    public function presentSiteMaps($siteMaps)
    {
        $this->siteMaps = array_flip( $siteMaps );
        $this->siteMapCount = count($siteMaps);
        echo "Going to traverse $this->siteMapCount sitemap(s):\n";
        foreach( $siteMaps as $siteMap ) {
            echo "$siteMap\n";
        }
        echo "--\n\n";
    }

    public function presentSiteMapUrls($siteMapUrl, $urls)
    {
        $this->siteMapUrl = $siteMapUrl;
        $this->urls = array_flip( $urls );
        $this->count = count($urls);
        echo "Going to warm cache of $this->count URL(s)\n";
    }
}

$run = function () {
    $retriever = new FileGetContentsUrlRetriever;
    $cacheWarmer = new CacheWarmer(
        new WarmUpCacheForSiteMap(
            new GetUrlsFromSiteMap($retriever),
            new WarmCacheOfOneUrl($retriever)
        ),
        new ArrayConfigProvider(require __DIR__ . '/config.php'),
        $retriever
    );
    $cacheWarmer->warmCaches(new EchoingCacheWarmerPresenter);
};
$run();
