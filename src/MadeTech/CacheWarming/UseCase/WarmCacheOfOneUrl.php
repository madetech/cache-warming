<?php
namespace MadeTech\CacheWarming\UseCase;

interface WarmCacheOfOneUrl
{
    public function visit($url);
}
