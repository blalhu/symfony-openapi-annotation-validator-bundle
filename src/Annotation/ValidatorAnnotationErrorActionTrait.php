<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

trait ValidatorAnnotationErrorActionTrait
{
    /**
     * @return string[]
     */
    public function getErrorActions(): array
    {
        return $this->errorActions;
    }
}
