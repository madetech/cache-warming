<?php

namespace MadeTech\CacheWarming\Test\Unit;


use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config\ConfigProvider;
use MadeTech\CacheWarming\Config\HrefLangExpansion;
use MadeTech\CacheWarming\Test\Acceptance\CacheWarmerPresenterStub;
use MadeTech\CacheWarming\UrlRetriever;
use MadeTech\CacheWarming\UseCase\WarmUpCacheForSiteMap;
use MadeTech\CacheWarming\UseCase\CacheWarmerPresenter;

class CacheWarmerTest extends \PHPUnit_Framework_TestCase implements WarmUpCacheForSiteMap, ConfigProvider, UrlRetriever
{

    /** @var CacheWarmer */
    private $useCase;

    /** @var string */
    private $siteMapUrls = [];

    /** @var string[] */
    private $config = [];

    /** @var HrefLangExpansion[] */
    private $hrefLangExpansions = [];

    /** @var string[] */
    private $getRequestUrls = [];

    /** @var boolean */
    private $finishCalledOnUrlRetriever = false;

    public function warmUpSiteCache($siteMapUrl, CacheWarmerPresenter $presenter)
    {
        $this->siteMapUrls[] = $siteMapUrl;
    }

    /** @return string[] */
    public function getSiteMapUrls()
    {
        return $this->config;
    }

    /** @return HrefLangExpansion[] */
    public function getHrefLangExpansions()
    {
        return $this->hrefLangExpansions;
    }

    private function warmCaches()
    {
        $this->useCase->warmCaches(new CacheWarmerPresenterStub);
    }

    private function assertSiteMapWasUsed($expected)
    {
        $this->assertContains($expected, $this->siteMapUrls);
    }

    private function assertSiteMapWasNotUsed($expected)
    {
        $this->assertNotContains($expected, $this->siteMapUrls);
    }

    private function setHrefLangExpansions($hrefLangExpansions)
    {
        $this->hrefLangExpansions = $hrefLangExpansions;
    }

    private function setConfigurationUrls($config)
    {
        $this->config = $config;
    }

    public function visit($url)
    {
        return $this->get($url);
    }

    public function get($url)
    {
        $this->getRequestUrls[] = $url;

        return <<<HTML
<!DOCTYPE html>
<head>
    <link rel="alternate" href="http://example.com/nl/nl/" hreflang="nl-NL"/>
    <link rel="alternate" href="http://example.com/gb/en/" hreflang="en-GB"/>
    <link rel="icon" href="http://example.com/icon.png" />
</head>
<body></body>
</html>
HTML;

    }

    public function finish(\Closure $onUrlVisited = null)
    {
        $this->finishCalledOnUrlRetriever = true;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->useCase = new CacheWarmer($this, $this, $this);
        $this->siteMapUrls = [];
        $this->config = [];
        $this->hrefLangExpansions = [];
        $this->getRequestUrls = [];
    }

    /** @test * */
    public function givenTwoSiteMapsInConfiguration_ThenWarmsUpCacheForBothSiteMaps()
    {
        $this->setConfigurationUrls([
            'http://example.com/sitemap.xml',
            'http://example.com/sitemap2.xml',
        ]);

        $this->warmCaches();

        $this->assertSiteMapWasUsed('http://example.com/sitemap.xml');
        $this->assertSiteMapWasUsed('http://example.com/sitemap2.xml');
    }

    /** @test * */
    public function givenHrefLangExpansion_ThenRequestsHtml()
    {
        $this->setHrefLangExpansions([
            new HrefLangExpansion('http://example.com/', '/(^.*$)/', '$1sitemap'),
            new HrefLangExpansion('http://example.com/', '/(^.*$)/', '$1sitemap.xml'),
        ]);

        $this->warmCaches();

        $this->assertTrue( $this->finishCalledOnUrlRetriever );
        $this->assertCount(1, $this->getRequestUrls);
        $this->assertContains('http://example.com/', $this->getRequestUrls);

        $this->assertSiteMapWasUsed('http://example.com/nl/nl/sitemap');
        $this->assertSiteMapWasUsed('http://example.com/gb/en/sitemap');
        $this->assertSiteMapWasUsed('http://example.com/gb/en/sitemap.xml');
        $this->assertSiteMapWasUsed('http://example.com/gb/en/sitemap.xml');
        $this->assertSiteMapWasNotUsed('http://example.com/icon.png');
    }
}
