<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;

class ExceptionActionController extends Controller
{
    /**
     * @Validator(
     *      providerName="default",
     *      errorActions={
     *           "@pelso.openapi_validator_bundle.error_action.exception"
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
