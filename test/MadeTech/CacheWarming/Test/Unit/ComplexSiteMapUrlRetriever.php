<?php
namespace MadeTech\CacheWarming\Test\Unit;

use MadeTech\CacheWarming\UrlRetriever;

class ComplexSiteMapUrlRetriever extends UrlRetrieverStub implements UrlRetriever
{
    public function get($url)
    {
        return file_get_contents(__DIR__ . '/../resources/complexSiteMap.xml');
    }
}
