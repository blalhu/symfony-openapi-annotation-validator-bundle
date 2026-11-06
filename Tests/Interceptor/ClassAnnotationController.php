<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Validator(
 *     providerName="class",
 *     errorActions={
 *         "@pelso.openapi_validator_bundle.error_action.log"
 *     }
 * )
 */
class ClassAnnotationController extends Controller
{
    public function defaultAction(): Response
    {
        return new Response();
    }
}
