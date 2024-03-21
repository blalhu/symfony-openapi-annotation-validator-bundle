<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;

class BadRequestResponseErrorAction implements ErrorActionInterface
{
    public function __construct()
    {
    }

    public function triggerAction(): void
    {
        // TODO: Implement triggerAction() method.
    }
}
