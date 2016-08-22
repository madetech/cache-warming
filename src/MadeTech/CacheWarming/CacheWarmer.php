<?php

namespace MadeTech\CacheWarming;


use MadeTech\CacheWarming\Config\HrefLangExpansion;

class CacheWarmer
{
    /** @var UseCase\WarmUpCacheForSiteMap */
    private $warmUpCacheForSite;

    /** @var Config\ConfigProvider */
    private $configuration;

    /** @var UrlRetriever */
    private $urlRetriever;

    /** @var string[] */
    private $htmlPages = [];

    public function __construct(
        UseCase\WarmUpCacheForSiteMap $warmUpCacheForSite,
        Config\ConfigProvider $configuration,
        UrlRetriever $urlRetriever
    ) {
        $this->warmUpCacheForSite = $warmUpCacheForSite;
        $this->configuration = $configuration;
        $this->urlRetriever = $urlRetriever;
    }

    public function warmCaches(UseCase\WarmUpCacheForSitePresenter $presenter)
    {
        $siteMaps = $this->getSiteMaps();
        foreach ($siteMaps as $siteMap) {
            $this->warmUpCacheForSite->warmUpSiteCache($siteMap, $presenter);
        }
    }

    /**
     * @param HrefLangExpansion $hrefLangExpansion
     *
     * @return string[]
     */
    private function getAlternateUrls(HrefLangExpansion $hrefLangExpansion)
    {
        $alternateUrls = [];
        foreach ($this->getLinkTagsFromSite($hrefLangExpansion) as $linkTag) {
            $attributes = $this->getLinkTagAttributes($linkTag);
            if ($this->isAlternate($attributes)) {
                $alternateUrls[] = (string)$attributes['href']->value;
            }
        }

        return $alternateUrls;
    }

    private function getHrefLangExpansionSiteMaps(HrefLangExpansion $hrefLangExpansion)
    {
        return array_map(
            function ($url) use ($hrefLangExpansion) {
                return preg_replace(
                    $hrefLangExpansion->getRegexMatchingString(),
                    $hrefLangExpansion->getRegexReplacingString(),
                    $url
                );
            },
            $this->getAlternateUrls($hrefLangExpansion)
        );
    }

    private function getSiteHtml(HrefLangExpansion $hrefLangExpansion)
    {
        $url = $hrefLangExpansion->getSiteUrl();
        if (!isset($this->htmlPages[$url])) {
            $this->htmlPages[$url] = $this->urlRetriever->get($url);
        }

        return $this->htmlPages[$url];
    }

    private function getDomDocument(HrefLangExpansion $hrefLangExpansion)
    {
        $dom = new \DomDocument();
        $dom->loadHTML($this->getSiteHtml($hrefLangExpansion));

        return $dom;
    }

    private function getLinkTagsFromSite(HrefLangExpansion $hrefLangExpansion)
    {
        return iterator_to_array($this->getDomDocument($hrefLangExpansion)->getElementsByTagName('link'));
    }

    private function getLinkTagAttributes($linkTag)
    {
        return iterator_to_array($linkTag->attributes);
    }

    private function isAlternate($attributes)
    {
        $rel = (string)$attributes['rel']->value;

        return $rel === 'alternate';
    }

    private function getAlternateSiteMaps()
    {
        $urls = [];
        foreach ($this->configuration->getHrefLangExpansions() as $hrefLangExpansion) {
            $urls = array_merge($urls, $this->getHrefLangExpansionSiteMaps($hrefLangExpansion));
        }

        return $urls;
    }

    private function getSiteMaps()
    {
        return array_merge($this->getAlternateSiteMaps(), $this->configuration->getSiteMapUrls());
    }
}
