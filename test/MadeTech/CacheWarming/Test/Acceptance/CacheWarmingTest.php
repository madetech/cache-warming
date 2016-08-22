<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config;
use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSite;
use MadeTech\HttpSimulator;

class CacheWarmingTest extends \PHPUnit_Framework_TestCase
{
    use HttpSimulator;

    public function setUpSimulatorResponse()
    {
        $this->setupSiteMap(__DIR__ . '/../resources/dutchSiteMap.xml', 'sitemap2.xml');
        $this->setupSiteMap(__DIR__ . '/../resources/complexSiteMap.xml', 'sitemap.xml');
    }

    private function setupSiteMap($siteMapXml, $destination)
    {
        $sitemapResource = file_get_contents($siteMapXml);
        $this->writeResponse($this->replaceDomains($sitemapResource), $destination);
    }

    private function replaceDomains($sitemapResource)
    {
        return str_replace('https://example.com', 'http://' . self::$domain,
            $sitemapResource);
    }

    /**
     * @param $domain
     */
    public function warmUpSiteCache($domain)
    {
        $useCase = $this->getUseCase();
        $useCase->warmUpSiteCache("http://$domain/sitemap.xml", new WarmUpCacheForSitePresenterStub);
    }

    /**
     * @return WarmUpCacheForSite
     */
    public function getUseCase()
    {
        $retriever = new FileGetContentsUrlRetriever();

        return new WarmUpCacheForSite(new GetUrlsFromSiteMap($retriever), new WarmCacheOfOneUrl($retriever));
    }

    /** @test * */
    public function givenAComplexSiteMap_WhenWarmUpCache_ThenExpectThoseUrlsToBeWarmed()
    {
        $this->setUpSimulatorResponse();
        $this->warmUpSiteCache(self::$domain);
        $this->assertRequestsToSimulatorWereMadeOnPaths([
            'gb/en/',
            'gb/en/legal/terms/',
            'gb/en/product/red-fleese/',
            'gb/en/product/blue-trousers/',
            'gb/en/joe/blog-tree/',
        ]);
    }

    /** @test * */
    public function givenBasicArrayConfig_ThenWarmCacheForSiteMap()
    {
        $this->setUpSimulatorResponse();
        $domain = self::$domain;
        $configuration = new Config\ArrayConfigProvider([
            "http://$domain/sitemap.xml",
            "http://$domain/sitemap2.xml",
        ]);
        $useCase = $this->getUseCase();
        $cacheWarmer = new CacheWarmer($useCase, $configuration);
        $cacheWarmer->warmCaches(new WarmUpCacheForSitePresenterStub);
        $this->assertRequestsToSimulatorWereMadeOnPaths([
            'gb/en/',
            'gb/en/legal/terms/',
            'gb/en/product/red-fleese/',
            'gb/en/product/blue-trousers/',
            'gb/en/joe/blog-tree/',
            'nl/nl/',
            'nl/nl/prijzen',
            'nl/nl/over-ons/',
        ]);
    }
}
