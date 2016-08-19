<?php
namespace MadeTech\CacheWarming;

class WarmCacheOfOneUrl implements UseCase\WarmCacheOfOneUrl
{
    /** @var UrlRetriever */
    private $httpClient;

    public function __construct(UrlRetriever $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function visit($url)
    {
        $this->httpClient->get($url);
    }
}
