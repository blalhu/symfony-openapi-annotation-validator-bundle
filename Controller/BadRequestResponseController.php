<?php

namespace Pelso\OpenAPIValidatorBundle\Controller;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BadRequestResponseController extends Controller
{
    public function badRequestAction(ValidationErrorException $exception): Response
    {
        return new JsonResponse(
            [
                'error_message' => $exception->getPrevious()->getMessage()
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
