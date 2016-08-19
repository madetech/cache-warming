<?php
namespace MadeTech\CacheWarming\Test\Unit;

use MadeTech\CacheWarming\UrlRetriever;

class UrlRetrieverSpy implements UrlRetriever
{

    /** @var string */
    private $url;

    public function get($url)
    {
        $this->url = $url;
        return '';
    }

    public function assertUrlIsEqualTo($expectedUrl)
    {
        \PHPUnit_Framework_TestCase::assertEquals( $expectedUrl, $this->url );
    }
}
