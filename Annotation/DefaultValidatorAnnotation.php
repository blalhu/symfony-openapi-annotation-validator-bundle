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
    private $errorAction;

    public function __construct()
    {
        $this->errorAction[] = BadRequestResponseErrorAction::class;
    }

    public function getProviderName(): string
    {
        return 'default';
    }
}
