<?php
namespace MadeTech\CacheWarming;

interface UrlRetriever
{
    public function get($url);

    public function visit($url);

    public function finish(\Closure $onUrlVisited = null);
}
