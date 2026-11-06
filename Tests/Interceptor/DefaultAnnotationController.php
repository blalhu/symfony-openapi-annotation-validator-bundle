<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @DefaultValidator()
 */
class DefaultAnnotationController extends Controller
{
    public function defaultAction(): Response
    {
        return new Response();
    }
}
