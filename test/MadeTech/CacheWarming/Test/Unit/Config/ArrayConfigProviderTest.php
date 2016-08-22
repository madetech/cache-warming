<?php

namespace MadeTech\CacheWarming\Test\Unit\Config;


use MadeTech\CacheWarming\Config\ArrayConfigProvider;

class ArrayConfigProviderTest extends \PHPUnit_Framework_TestCase
{

    /** @test * */
    public function testThatItReturnsTheConfigPassedIn()
    {
        $configProvider = new ArrayConfigProvider(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $configProvider->getSiteMapUrls());
    }

}
