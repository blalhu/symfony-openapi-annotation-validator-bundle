<?php

namespace Pelso\OpenAPIValidatorBundle\Controller;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BadRequestResponseController extends Controller
{
    public function badRequestAction(
        ValidationErrorException $exception,
        string $formattedErrorMessage
    ): Response {
        return new JsonResponse(
            [
                'error_message' => $formattedErrorMessage
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
