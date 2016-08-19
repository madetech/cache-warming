<?php
namespace MadeTech\CacheWarming\UseCase;

interface GetUrlsFromSiteMap
{
    /**
     * @param $siteMapUrl
     *
     * @return string[]
     * @throws \Exception
     */
    public function getUrlsFromSiteMap($siteMapUrl);
}
