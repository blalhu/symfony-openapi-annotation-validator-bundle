<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\spec\OpenApi;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException;

abstract class AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /** @var array */
    protected $openAPIArray;

    /** @var ValidatorBuilder */
    protected $validatorBuilder;

    /**
     * @throws TypeErrorException
     * @throws InvalidOpenAPISchemeException
     */
    public function __construct(array $openAPIArray)
    {
        $this->openAPIArray = $openAPIArray;
        $openAPI = new OpenApi($this->openAPIArray);
        if (!$openAPI->validate()) {
            throw new InvalidOpenAPISchemeException();
        }

        $this->validatorBuilder = (new ValidatorBuilder())->fromSchema($openAPI);
    }

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
        return $this->validatorBuilder;
    }
}
