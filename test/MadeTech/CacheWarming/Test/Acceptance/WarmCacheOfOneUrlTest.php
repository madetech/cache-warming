<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\Gateway\FileGetContentsUrlRetriever;
use MadeTech\CacheWarming\WarmCacheOfOneUrl;
use MadeTech\HttpSimulator;

class WarmCacheOfOneUrlTest extends \PHPUnit_Framework_TestCase
{
    use HttpSimulator { setUp as private simulatorSetUp; }

    /** @var WarmCacheOfOneUrl */
    private $usecase;

    public function assertOneRequestWasMade()
    {
        $requestMade = glob($this->getRequestsPath().'*');
        $this->assertCount(1, $requestMade);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->simulatorSetUp();
        $this->usecase = new WarmCacheOfOneUrl(new FileGetContentsUrlRetriever );
    }

    /** @test */
    public function givenAUrl_ThenRequestsThatUrl()
    {
        $domain = self::$domain;
        $this->usecase->visit( "http://$domain/" );
        $this->assertOneRequestWasMade();
    }
}
