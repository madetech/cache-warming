<?php
namespace MadeTech\CacheWarming\Test\Unit;

use MadeTech\CacheWarming\GetUrlsFromSiteMap;

class GetUrlsFromSiteMapTest extends \PHPUnit_Framework_TestCase
{
    const SITEMAP_URL = 'sitemap url';

    /**
     * @param $urlRetriever
     *
     * @return \string[]
     */
    public function getUrlsFromSiteMap($urlRetriever)
    {
        return (new GetUrlsFromSiteMap($urlRetriever))->getUrlsFromSiteMap(self::SITEMAP_URL);
    }

    /** @test */
    public function givenNoSitemapAtUrl_ThenExpectNoUrls()
    {
        $this->assertEquals([], $this->getUrlsFromSiteMap(new UrlRetrieverStub));
    }

    /** @test */
    public function givenASitemap_ThenExpectUrls()
    {
        $this->assertEquals([SimpleSiteMapUrlRetriever::LOCATION],
            $this->getUrlsFromSiteMap(new SimpleSiteMapUrlRetriever));
    }

    /** @test */
    public function givenAComplexSitemap_ThenExpectUrls()
    {
        $this->assertEquals(
            [
                'https://example.com/gb/en/',
                'https://example.com/gb/en/legal/terms',
                'https://example.com/gb/en/product/red-fleese',
                'https://example.com/gb/en/product/blue-trousers',
                'https://example.com/gb/en/joe/blog-tree',
            ],
            $this->getUrlsFromSiteMap(new ComplexSiteMapUrlRetriever)
        );
    }

    /** @test * */
    public function givenASiteMapUrl_CallsUrlResourceRetrieverWithUrl()
    {
        $spy = new UrlRetrieverSpy;
        $url = 'url://this/is/a/url';
        (new GetUrlsFromSiteMap($spy))->getUrlsFromSiteMap($url);
        $spy->assertUrlIsEqualTo($url);
    }

}
