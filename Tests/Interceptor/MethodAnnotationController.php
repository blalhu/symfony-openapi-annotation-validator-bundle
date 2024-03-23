<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MethodAnnotationController extends Controller
{
    /**
     * @ValidatorAnnotation(
     *     providerName="method",
     *     errorActions={
     *         "@pelso.openapi_validator_bundle.error_action.log"
     *     }
     * )
     * @return Response
     */
    public function defaultAction(): Response
    {
        return new Response();
    }
}
