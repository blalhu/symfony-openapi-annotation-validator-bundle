<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;

class ValidatorAnnotationController extends Controller
{
    /**
     * @Validator(
     *      providerName="default",
     *      errorActions={
     *           "@pelso.openapi_validator_bundle.error_action.bad_request_response",
     *           "@pelso.openapi_validator_bundle.error_action.log"
     *      }
     * )
     *
     * @return Response
     */
    public function postAction(): Response
    {
        return new JsonResponse();
    }
}