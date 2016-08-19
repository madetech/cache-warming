<?php
namespace MadeTech\CacheWarming\Gateway;

use MadeTech\CacheWarming\UrlRetriever;

class FileGetContentsUrlRetriever implements UrlRetriever
{
    public function get($url)
    {
        return file_get_contents($url);
    }
}
