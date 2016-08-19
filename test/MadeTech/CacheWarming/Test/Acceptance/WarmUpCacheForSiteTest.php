<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSite;
use MadeTech\HttpSimulator;

class WarmUpCacheForSiteTest extends \PHPUnit_Framework_TestCase
{
    use HttpSimulator;

    /**
     * @param $siteMapXml
     */
    public function setUpSimulatorResponse($siteMapXml)
    {
        $this->writeResponse($siteMapXml, 'sitemap.xml');
    }

    /**
     * @return WarmUpCacheForSite
     */
    public function getUseCase()
    {
        $retriever = new FileGetContentsUrlRetriever();
        return new WarmUpCacheForSite(new GetUrlsFromSiteMap($retriever), new WarmCacheOfOneUrl($retriever));
    }

    /**
     * @param $domain
     */
    public function warmUpSiteCache($domain)
    {
        $useCase = $this->getUseCase();
        $useCase->warmUpSiteCache("http://$domain/sitemap.xml", new WarmUpCacheForSitePresenterStub);
    }

    public function assertTwoRequestsWereMade()
    {
        $requestMade = glob($this->getRequestsPath().'path/*');
        $this->assertCount(2, $requestMade);
    }

    /** @test **/
    public function givenTwoUrls_WhenWarmUpCache_ThenExpectThoseUrlsToBeWarmed() {
        $domain = self::$domain;
        $siteMapXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://{$domain}/path/1</loc>
        <loc>http://{$domain}/path/2</loc>
    </url>
</urlset>
XML;

        $this->setUpSimulatorResponse($siteMapXml);

        $this->warmUpSiteCache($domain);

        $this->assertTwoRequestsWereMade();
    }
}
