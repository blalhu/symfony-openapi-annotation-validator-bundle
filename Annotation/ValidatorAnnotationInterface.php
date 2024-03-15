<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

interface ValidatorAnnotationInterface
{
    public function getProviderName(): string;

    public function getErrorActions(): array;
}
