<?php

namespace MadeTech\CacheWarming\Test\Unit;


use MadeTech\CacheWarming\CacheWarmer;
use MadeTech\CacheWarming\Config\ConfigProvider;
use MadeTech\CacheWarming\Test\Acceptance\WarmUpCacheForSitePresenterStub;
use MadeTech\CacheWarming\UseCase\WarmUpCacheForSite;
use MadeTech\CacheWarming\UseCase\WarmUpCacheForSitePresenter;

class CacheWarmerTest extends \PHPUnit_Framework_TestCase implements WarmUpCacheForSite, ConfigProvider
{

    /** @var CacheWarmer */
    private $useCase;

    /** @var string */
    private $siteMapUrls = [];

    /** @var string[] */
    private $config;

    public function warmUpSiteCache($siteMapUrl, WarmUpCacheForSitePresenter $presenter)
    {
        $this->siteMapUrls[] = $siteMapUrl;
    }

    /** @var string */
    public function getSiteMapUrls()
    {
        return $this->config;
    }

    private function warmCaches()
    {
        $this->useCase->warmCaches(new WarmUpCacheForSitePresenterStub);
    }

    private function assertSiteMapWasUsed($expected)
    {
        $this->assertContains($expected, $this->siteMapUrls);
    }

    private function setUpConfig($config)
    {
        $this->config = $config;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->useCase = new CacheWarmer($this, $this);
    }

    /** @test * */
    public function given()
    {
        $this->setUpConfig([
            'http://example.com/sitemap.xml',
            'http://example.com/sitemap2.xml',
        ]);

        $this->warmCaches();

        $this->assertSiteMapWasUsed('http://example.com/sitemap.xml');
        $this->assertSiteMapWasUsed('http://example.com/sitemap2.xml');
    }
}
