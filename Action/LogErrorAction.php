<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

class LogErrorAction implements ErrorActionInterface
{
    public function __construct()
    {
    }

    public function triggerAction(): void
    {
        // TODO: Implement triggerAction() method.
    }
}
