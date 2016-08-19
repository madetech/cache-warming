<?php
namespace MadeTech\CacheWarming;

class GetUrlsFromSiteMap implements UseCase\GetUrlsFromSiteMap
{
    const XPATH_LOC = '//a:loc';
    /** @var UrlRetriever */
    private $urlRetriever;

    public function __construct(UrlRetriever $urlRetriever)
    {
        $this->urlRetriever = $urlRetriever;
    }

    /**
     * @param $siteMapUrl
     *
     * @return string[]
     * @throws \Exception
     */
    public function getUrlsFromSiteMap($siteMapUrl)
    {
        $xmlDocument = $this->urlRetriever->get($siteMapUrl);
        try {
            $reader = $this->getXmlReader($xmlDocument);

            return $this->getUrlsAsStrings($this->getLocElements($reader));
        } catch (\Exception $e) {
            if (!$this->isInvalidXmlException($e)) {
                throw $e;
            }

            return [];
        }
    }

    /**
     * @param $xmlElements
     *
     * @return string[]
     */
    public function getUrlsAsStrings($xmlElements)
    {
        /** @var string[] $return */
        $stringUrls = [];
        foreach ($xmlElements as $xmlElement) {
            $stringUrls[] = (string)$xmlElement;
        }

        return $stringUrls;
    }

    /**
     * @param $e
     *
     * @return bool
     */
    public function isInvalidXmlException(\Exception $e)
    {
        return $e->getMessage() == 'String could not be parsed as XML';
    }

    /**
     * @param $response
     *
     * @return \SimpleXMLElement
     */
    public function getXmlReader($response)
    {
        $xml = new \SimpleXMLElement($response);
        $this->registerNamespaces($xml);

        return $xml;
    }

    public function getLocElements(\SimpleXMLElement $reader)
    {
        return $reader->xpath(self::XPATH_LOC);
    }

    public function registerNamespaces(\SimpleXMLElement $xml)
    {
        foreach ($xml->getDocNamespaces() as $xpathPrefix => $namespace) {
            $noPrefix = strlen($xpathPrefix) == 0;
            if ($noPrefix) {
                $xpathPrefix = "a";
            }
            $xml->registerXPathNamespace($xpathPrefix, $namespace);
        }
    }
}
