<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

trait ValidatorAnnotationErrorActionTrait
{
    /**
     * @return ErrorActionInterface[]
     */
    public function getErrorActions(): array
    {
        return $this->errorAction; //TODO: give back service instances!!!
    }
}
