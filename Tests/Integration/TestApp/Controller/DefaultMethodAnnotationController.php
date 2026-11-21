<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;

class DefaultMethodAnnotationController extends Controller
{
    /**
     * @DefaultValidator
     *
     * @return Response
     */
    public function postAction(): Response
    {
        return new JsonResponse();
    }
}
