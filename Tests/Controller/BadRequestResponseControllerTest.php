<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Controller;

use Pelso\OpenAPIValidatorBundle\Controller\BadRequestResponseController;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BadRequestResponseControllerTest extends TestCase
{

    public function testResponseFormat()
    {
        $response = (new BadRequestResponseController())
            ->badRequestAction(
                new ValidationErrorException(
                    new \Exception('original_test_message'),
                    'test_message'
                )
            );

        $this->assertEquals(get_class($response), JsonResponse::class);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseArray = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('error_message', $responseArray));
        $this->assertEquals('original_test_message', $responseArray['error_message']);
    }
}
