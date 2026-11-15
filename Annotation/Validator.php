<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\DependencyInjection\BundleConfiguration;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class Validator implements ValidatorAnnotationInterface
{
    use ValidatorAnnotationErrorActionTrait;

    /** @var string */
    public $providerName;

    /** @var array */
    public $errorActions;

    /** @var string */
    public $errorMessageFormatter = '@' . BundleConfiguration::ERROR_MESSAGE_FORMATTER;

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getErrorMessageFormatter(): string
    {
        return $this->errorMessageFormatter;
    }
}
