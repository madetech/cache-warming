<?php
namespace MadeTech\CacheWarming\UseCase;

interface WarmUpCacheForSite
{
    public function warmUpSiteCache($siteMapUrl, WarmUpCacheForSitePresenter $presenter);
}
