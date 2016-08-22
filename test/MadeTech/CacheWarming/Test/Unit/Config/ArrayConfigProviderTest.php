<?php

namespace MadeTech\CacheWarming\Test\Unit\Config;


use MadeTech\CacheWarming\Config\ArrayConfigProvider;
use MadeTech\CacheWarming\Config\HrefLangExpansion;

class ArrayConfigProviderTest extends \PHPUnit_Framework_TestCase
{

    /** @test * */
    public function testThatItReturnsTheConfigPassedIn()
    {
        $configProvider = new ArrayConfigProvider(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $configProvider->getSiteMapUrls());
    }

    /** @test * */
    public function givenHrefLangExpansionInConfig_ThenDoNotProvideForCallToGetSiteMapUrls()
    {
        $configProvider = new ArrayConfigProvider([new HrefLangExpansion('', '', '')]);
        $this->assertEquals([], $configProvider->getSiteMapUrls());
    }

    /** @test * */
    public function givenNoHrefLangExpansionInConfig_ThenShouldReturnEmptyArray()
    {
        $configProvider = new ArrayConfigProvider(['a', 'b', 'c']);
        $this->assertEquals([], $configProvider->getHrefLangExpansions());
    }

    /** @test * */
    public function givenHrefLangExpansionInConfig_ThenShouldReturnIt()
    {
        $expansion = new HrefLangExpansion('', '', '');
        $configProvider = new ArrayConfigProvider(['a', 'b', 'c', $expansion]);
        $hrefLangExpansions = $configProvider->getHrefLangExpansions();
        $this->assertSame($expansion, $hrefLangExpansions[0]);
    }


}
