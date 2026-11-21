<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Validator(
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
