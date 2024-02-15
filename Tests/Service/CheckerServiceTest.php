<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Service;

use Pelso\OpenAPIValidatorBundle\Service\CheckerService;
use PHPUnit\Framework\TestCase;

class CheckerServiceTest extends TestCase
{
    public function testMethodExistence()
    {
        $this->assertEquals(true, method_exists(CheckerService::class, 'check'));
    }
}