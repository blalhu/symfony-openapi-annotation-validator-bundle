<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class ValidatorAnnotation implements ValidatorAnnotationInterface
{
    use ValidatorAnnotationErrorActionTrait;

    /** @var string */
    public $providerName;

    /** @var array */
    public $errorActions;

    public function getProviderName(): string
    {
        return $this->providerName;
    }
}
