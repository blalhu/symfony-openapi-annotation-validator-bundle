<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Validator;

use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Provider\AbstractOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestValidatorTest extends TestCase
{

    public function testValidator(): void
    {
        $this->assertEquals(
            null,
            (new RequestValidator())->validate(
                Request::create('http://127.0.0.1/foo', 'GET'),
                $this->createMock(AbstractOpenAPIProvider::class)
            )
        );
    }

    public function testNoPathMatch(): void
    {
        $provider = $this->createMock(AbstractOpenAPIProvider::class);
        $provider->method('getValidatorBuilder')
            ->willThrowException(new NoPath());
        try {
            (new RequestValidator())->validate(
                Request::create('http://127.0.0.1/foo', 'GET'),
                $provider
            );

            $this->assertTrue(true);
        } catch (\Throwable $throwable) {
            $this->assertTrue(false);
        }
    }

    public function testInvalidRequestException(): void
    {
        $this->expectException(ValidationErrorException::class);

        $provider = $this->createMock(AbstractOpenAPIProvider::class);
        $provider->method('getValidatorBuilder')
            ->willThrowException(new ValidationFailed());
        (new RequestValidator())->validate(
            Request::create('http://127.0.0.1/foo', 'GET'),
            $provider
        );
    }
}
