<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidatorAnnotation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @DefaultValidatorAnnotation()
 */
class DefaultAnnotationController extends Controller
{
    public function defaultAction(): Response
    {
        return new Response();
    }
}
