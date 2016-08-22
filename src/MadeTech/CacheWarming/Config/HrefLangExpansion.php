<?php

namespace MadeTech\CacheWarming\Config;


class HrefLangExpansion
{

    /** @var string */
    private $siteUrl;

    /** @var string */
    private $regexMatchingString;

    /** @var string */
    private $regexReplacingString;


    /**
     * @param string $siteUrl
     * @param string $regexMatchingString
     * @param string $regexReplacingString
     */
    public function __construct($siteUrl, $regexMatchingString, $regexReplacingString)
    {
        $this->siteUrl = $siteUrl;
        $this->regexMatchingString = $regexMatchingString;
        $this->regexReplacingString = $regexReplacingString;
    }

    /** @return string */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /** @return string */
    public function getRegexMatchingString()
    {
        return $this->regexMatchingString;
    }

    /** @return string */
    public function getRegexReplacingString()
    {
        return $this->regexReplacingString;
    }
}
