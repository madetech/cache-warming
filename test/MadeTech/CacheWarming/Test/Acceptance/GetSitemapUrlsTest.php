<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\GetUrlsFromSiteMap;
use MadeTech\HttpSimulator;

class GetSitemapUrlsTest extends \PHPUnit_Framework_TestCase
{

    use HttpSimulator;

    public function setUpSimulatorResponse()
    {
        $this->writeResponse(file_get_contents(__DIR__ . '/../resources/complexSiteMap.xml'), 'sitemap.xml');
    }

    /**
     * @param $domain
     *
     * @return string[]
     */
    public function getUrlsFromSiteMap($domain)
    {
        return (new GetUrlsFromSiteMap(new FileGetContentsUrlRetriever))
            ->getUrlsFromSiteMap("http://$domain/sitemap.xml");
    }

    /**
     * @param $expectedUrls
     * @param string[] $urls
     */
    public function assertAllExpectedUrlsPresentInArray($expectedUrls, $urls)
    {
        foreach ($expectedUrls as $expectedUrl) {
            $this->assertFalse(array_search($expectedUrl, $urls) === false);
        }
    }

    /** @test * */
    public function givenASitemap_ThenReturnsUrls()
    {
        $this->setUpSimulatorResponse();

        $urls = $this->getUrlsFromSiteMap(self::$domain);

        $this->assertAllExpectedUrlsPresentInArray(
            [
                'https://example.com/gb/en/',
                'https://example.com/gb/en/legal/terms',
                'https://example.com/gb/en/product/red-fleese',
                'https://example.com/gb/en/product/blue-trousers',
                'https://example.com/gb/en/joe/blog-tree',
            ],
            $urls
        );
    }
}
