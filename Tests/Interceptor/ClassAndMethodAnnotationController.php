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
class ClassAndMethodAnnotationController extends Controller
{
    /**
     * @Validator(
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
