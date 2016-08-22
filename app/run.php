#!/usr/bin/env php
<?php
use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config\ArrayConfigProvider;
use MadeTech\CacheWarming\Gateway\GuzzleUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\UseCase\CacheWarmerPresenter;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSiteMap;

if(!defined('COMPOSER_LOADED') || !COMPOSER_LOADED) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class EchoingCacheWarmerPresenter implements CacheWarmerPresenter
{
    /** @var int */
    private $count = 0;

    /** @var int */
    private $numberProcessed = 0;

    public function presentVisitedUrl($url)
    {
    }

    public function presentSiteMaps($siteMaps)
    {
        $count = count($siteMaps);
        echo "Going to traverse $count sitemap(s):\n";
        echo implode("\n", $siteMaps), "\n--\n\n";
    }

    public function presentSiteMapUrls($siteMapUrl, $urls)
    {
        $this->count += count($urls);
        echo "Found $this->count URL(s) to warm\n";
    }

    public function presentUrlProcessed($url)
    {
        $index = $this->numberProcessed++;
        $percentage = round(($index / $this->count) * 100);
        echo "$percentage% $index/{$this->count}: $url\n";
    }
}

$run = function () {
    $argv = $GLOBALS['argv'];
    $lastArgument = $argv[count($argv)-1];

    if( file_exists( $lastArgument ) && $lastArgument !== __FILE__ ) {
        $configFile = $lastArgument;
    } else {
        $configFile = __DIR__ . '/config.php';
    }

    $retriever = new GuzzleUrlRetriever;
    $cacheWarmer = new CacheWarmer(
        new WarmUpCacheForSiteMap(
            new GetUrlsFromSiteMap($retriever),
            new WarmCacheOfOneUrl($retriever)
        ),
        new ArrayConfigProvider(require $configFile),
        $retriever
    );
    $cacheWarmer->warmCaches(new EchoingCacheWarmerPresenter);
};
$run();
