<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Annotation;

use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;

/**
 * @ValidatorAnnotation(
 *     providerName="provider_name",
 *     errorAction={
 *          "Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction",
 *          "Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction"
 *      }
 * )
 */
class ValidatorAnnotationChecker
{

}
