<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;

abstract class AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /** @var string */
    protected $openAPIContent;

    /** @var array */
    protected $openAPIArray;

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->openAPIArray;
    }

    /**
     * @return ValidatorBuilder
     */
    public function getValidatorBuilder(): ValidatorBuilder
    {
        return (new ValidatorBuilder())->fromYaml($this->openAPIContent);
    }
}