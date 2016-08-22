<?php

namespace MadeTech\CacheWarming\Test\Unit\Gateway;


use MadeTech\CacheWarming\Gateway\GuzzleUrlRetriever;
use MadeTech\HttpSimulator;

class GuzzleUrlRetrieverTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_TIMEOUT = 60;

    use HttpSimulator {
        tearDown as private simulatorTearDown;
        setUp as private simulatorSetUp;
    }

    private function setSocketTimeout($timeout)
    {
        GuzzleUrlRetriever::$timeout = $timeout;
        ini_set('default_socket_timeout', $timeout);
    }

    private function assertDoesNotThrowException($function)
    {
        $e = null;
        try {
            $function();
        } catch (\Exception $e) {

        }
        $this->assertNull($e);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->simulatorSetUp();
        \PHPUnit_Framework_Error_Warning::$enabled = false;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->simulatorTearDown();
        $this->setSocketTimeout(self::DEFAULT_TIMEOUT);
        \PHPUnit_Framework_Error_Warning::$enabled = true;
    }

    /** @test */
    public function testThatTcpTimeoutSilentlyFails()
    {
        $this->setSocketTimeout(1);
        $this->assertDoesNotThrowException(function () {
            $domain = self::$domain;
            (new GuzzleUrlRetriever())->get("http://$domain/timeout");
        });
    }
}
