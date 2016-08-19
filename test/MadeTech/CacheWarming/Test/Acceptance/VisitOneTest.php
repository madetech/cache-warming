<?php
namespace MadeTech\CacheWarming\Test\Acceptance;

use MadeTech\CacheWarming\HttpClient;
use MadeTech\CacheWarming\VisitOne;

class VisitOneTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function givenAUrl_ThenRequestsThatUrl()
    {
        new VisitOne( new HttpClient );
    }

}