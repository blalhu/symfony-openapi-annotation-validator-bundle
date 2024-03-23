<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class NotRegisteredValidatorAnnotation implements ValidatorAnnotationInterface
{

    public function getProviderName(): string
    {
        return 'not-registered';
    }

    public function getErrorActions(): array
    {
        return ['not-registered'];
    }
}
