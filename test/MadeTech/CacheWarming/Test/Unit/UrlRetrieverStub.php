<?php
namespace MadeTech\CacheWarming\Test\Unit;


use MadeTech\CacheWarming\UrlRetriever;

class UrlRetrieverStub implements UrlRetriever
{
    public function get($url)
    {
    }

    public function visit($url)
    {
        return $this->get($url);
    }

    public function finish(\Closure $onUrlVisited = null)
    {

    }
}
