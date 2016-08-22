<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config;
use MadeTech\CacheWarming\Gateway\GuzzleUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSiteMap;
use MadeTech\HttpSimulator;

class CacheWarmingTest extends \PHPUnit_Framework_TestCase
{
    private $retriever;
    private $warmUpCacheForSiteMap;
    use HttpSimulator {
        setUp as private simulatorSetUp;
    }

    public function setUpSimulatorResponse()
    {
        $this->setupSiteMapResponseByFileName(__DIR__ . '/../resources/dutchSiteMap.xml', 'sitemap2.xml');
        $this->setupSiteMapResponseByFileName(__DIR__ . '/../resources/complexSiteMap.xml', 'sitemap.xml');
    }

    private function setupSiteMapResponseByFileName($siteMapFileName, $destination)
    {
        $this->setupSiteMapResponseByContents(file_get_contents($siteMapFileName), $destination);
    }

    private function setupSiteMapResponseByContents($siteMapContent, $destination)
    {
        $this->writeResponse($this->replaceDomains($siteMapContent), $destination);
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
        $useCase = $this->getWarmUpCacheForSiteMap();
        $useCase->warmUpSiteCache("http://$domain/sitemap.xml", new CacheWarmerPresenterStub);
    }

    /**
     * @return WarmUpCacheForSiteMap
     */
    private function getWarmUpCacheForSiteMap()
    {
        return $this->warmUpCacheForSiteMap;
    }

    private function warmCaches($configuration)
    {
        (new CacheWarmer($this->getWarmUpCacheForSiteMap(), $configuration, $this->retriever))
            ->warmCaches(new CacheWarmerPresenterStub);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->simulatorSetUp();
        $this->retriever = new GuzzleUrlRetriever();
        $this->warmUpCacheForSiteMap = new WarmUpCacheForSiteMap(
            new GetUrlsFromSiteMap($this->retriever),
            new WarmCacheOfOneUrl($this->retriever)
        );
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
        $this->warmCaches($configuration);
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

    /** @test * */
    public function givenHrefLangExpansionConfig_ThenWarmCacheForThoseSiteMaps()
    {
        $html = <<<HTML
<!DOCTYPE html>
<head>
    <link rel="alternate" href="https://example.com/nl/nl/" hreflang="nl-NL"/>
    <link rel="alternate" href="https://example.com/gb/en/" hreflang="en-GB"/>
</head>
<body></body>
</html>
HTML;

        $dutchSiteMap = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com/nl/nl/home/</loc>
        <loc>https://example.com/nl/nl/prijzen/</loc>
    </url>
</urlset>
XML;

        $britishSiteMap = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com/gb/en/home/</loc>
        <loc>https://example.com/gb/en/pricing/</loc>
    </url>
</urlset>
XML;

        $this->setupSiteMapResponseByContents($html, 'index.html');
        $this->setupSiteMapResponseByContents($dutchSiteMap, 'dutchSiteMap.xml');
        $this->setupSiteMapResponseByContents($britishSiteMap, 'britishSiteMap.xml');

        $domain = self::$domain;
        $configuration = new Config\ArrayConfigProvider([
            new Config\HrefLangExpansion("http://$domain/", '/(^.*$)/', '$1sitemap.xml'),
        ]);
        $this->warmCaches($configuration);
        $this->assertRequestsToSimulatorWereMadeOnPaths([
            'gb/en/home/',
            'gb/en/pricing/',
            'nl/nl/home/',
            'nl/nl/prijzen',
        ]);
    }
}
