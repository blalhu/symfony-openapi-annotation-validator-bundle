<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NoAnnotationController extends Controller
{
    public function defaultAction(): Response
    {
        return new Response();
    }
}
