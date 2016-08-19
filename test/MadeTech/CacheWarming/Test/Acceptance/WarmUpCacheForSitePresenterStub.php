<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\UseCase\WarmUpCacheForSitePresenter;

class WarmUpCacheForSitePresenterStub implements WarmUpCacheForSitePresenter
{
    public function present($url)
    {
    }
}
