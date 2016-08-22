<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\UseCase\CacheWarmerPresenter;

class CacheWarmerPresenterStub implements CacheWarmerPresenter
{
    public function presentVisitedUrl($url)
    {
    }

    public function presentSiteMaps($siteMaps)
    {
    }

    public function presentSiteMapUrls($siteMapUrl, $urls)
    {

    }

    public function presentUrlProcessed($url)
    {

    }
}
