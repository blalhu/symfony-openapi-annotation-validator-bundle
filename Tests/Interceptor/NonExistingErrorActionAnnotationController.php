<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ValidatorAnnotation(
 *     providerName="default",
 *     errorActions={
 *         "@non-existing"
 *     }
 * )
 */
class NonExistingErrorActionAnnotationController extends Controller
{
    public function defaultAction(): Response
    {
        return new Response();
    }
}
