<?php

namespace MadeTech\CacheWarming\Test\Unit;

use MadeTech\CacheWarming\Test\Acceptance\WarmUpCacheForSitePresenterStub;
use MadeTech\CacheWarming\UseCase\GetUrlsFromSiteMap;
use MadeTech\CacheWarming\UseCase\WarmCacheOfOneUrl;
use MadeTech\CacheWarming\WarmUpCacheForSiteMap;

class WarmUpCacheForSiteTest extends \PHPUnit_Framework_TestCase implements WarmCacheOfOneUrl, GetUrlsFromSiteMap
{

    /** @var string[] */
    private $visited = [];

    /** @var string[] */
    private $toVisit = [];

    /** @var  string */
    private $siteMapUrl;

    /**
     * @param $siteMapUrl
     *
     * @return string[]
     */
    public function getUrlsFromSiteMap($siteMapUrl)
    {
        $this->siteMapUrl = $siteMapUrl;
        return $this->toVisit;
    }

    public function visit($url)
    {
        $this->visited[] = $url;
    }

    /** @test * */
    public function testThatUseCaseIsWiredUpCorrectly()
    {
        $useCase = new WarmUpCacheForSiteMap($this, $this);

        $siteMapUrl = 'http://example.com/sitemap';
        $this->toVisit[] = 'http://example.com/a/path/1';
        $this->toVisit[] = 'http://example.com/a/path/2';

        $useCase->warmUpSiteCache($siteMapUrl, new WarmUpCacheForSitePresenterStub);

        $this->assertEquals( $siteMapUrl, $this->siteMapUrl );
        $this->assertEquals( $this->toVisit, $this->visited );
    }
}
