<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class DefaultValidatorAnnotation implements ValidatorAnnotationInterface
{
    use ValidatorAnnotationErrorActionTrait;

    /** @var array */
    private $errorActions;

    public function __construct()
    {
        $this->errorActions = [
            '@pelso.openapi_validator_bundle.error_action.bad_request_response',
            '@pelso.openapi_validator_bundle.error_action.log',
        ];
    }

    public function getProviderName(): string
    {
        return 'default';
    }
}
