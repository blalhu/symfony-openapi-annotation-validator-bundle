<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\Annotation;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;

/**
 * @Validator(
 *     providerName="provider_name",
 *     errorActions={
 *          "@pelso.openapi_validator_bundle.error_action.header_notice",
 *          "@pelso.openapi_validator_bundle.error_action.log"
 *      },
 *     errorMessageFormatter="@my.custom.message.formatter"
 * )
 */
class ValidatorAnnotationCheckerWithFormatter
{

    public function defaultAction()
    {
    }
}
