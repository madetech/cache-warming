<?php
namespace MadeTech\CacheWarming\UseCase;

interface CacheWarmerPresenter
{
    public function presentVisitedUrl($url);

    public function presentSiteMaps($siteMaps);

    public function presentSiteMapUrls($siteMapUrl, $urls);
}
