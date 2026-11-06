<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Annotation;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;

/**
 * @Validator(
 *     providerName="provider_name",
 *     errorActions={
 *          "@pelso.openapi_validator_bundle.error_action.header_notice",
 *          "@pelso.openapi_validator_bundle.error_action.log"
 *      }
 * )
 */
class ValidatorAnnotationChecker
{

    public function defaultAction()
    {
    }
}
