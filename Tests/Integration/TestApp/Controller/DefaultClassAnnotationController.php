<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;

/**
 * @DefaultValidator
 */
class DefaultClassAnnotationController extends Controller
{
    public function postAction(): Response
    {
        return new JsonResponse();
    }
}
