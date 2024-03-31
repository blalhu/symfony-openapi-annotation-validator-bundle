<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Action\ErrorActionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;

class HeaderNoticeErrorAction implements ErrorActionInterface
{
    public function __construct()
    {
    }

    public function triggerAction(ValidationErrorException $exception): void
    {
        // TODO: Implement triggerAction() method.
    }
}
