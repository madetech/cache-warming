<?php
namespace MadeTech\CacheWarming\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use MadeTech\CacheWarming\UrlRetriever;

class GuzzleUrlRetriever implements UrlRetriever
{
    public static $timeout = 60;

    const NUMBER_OF_CONCURRENT_REQUESTS = 10;

    public function get($url)
    {
        return @file_get_contents($url);
    }

    private $urlsToVisit = [];

    public function visit($url)
    {
        $this->urlsToVisit[] = $url;
    }

    public function finish(\Closure $onUrlVisited = null)
    {
        $client = new Client();

        $requests = function () {
            foreach ( $this->urlsToVisit as $url ) {
                yield new Request('GET', $url);
            }
        };

        $pool = new Pool($client, $requests(), [
            'concurrency' => self::NUMBER_OF_CONCURRENT_REQUESTS,
            'fulfilled' => function( $response, $index ) use ($onUrlVisited) {
                $onUrlVisited( $this->urlsToVisit[$index] );
            }
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }
}
