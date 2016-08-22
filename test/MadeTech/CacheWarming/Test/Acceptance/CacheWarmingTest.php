<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

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
        $sitemapResource = $this->getSitemapResource();
        $this->writeResponse($this->replaceDomains($sitemapResource), 'sitemap.xml');
    }

    private function replaceDomains($sitemapResource)
    {
        return str_replace('https://example.com', 'http://' . self::$domain,
            $sitemapResource);
    }

    private function getSitemapResource()
    {
        return file_get_contents(__DIR__ . '/../resources/complexSiteMap.xml');
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
}
