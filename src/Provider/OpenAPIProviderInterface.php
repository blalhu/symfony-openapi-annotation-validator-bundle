<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;

interface OpenAPIProviderInterface
{
    public function getArray(): array;

    public function getValidatorBuilder(): ValidatorBuilder;
}
