<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use cebe\openapi\exceptions\TypeErrorException;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Exceptions\ResourceParseError;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;

class JSONOpenAPIProvider extends AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /**
     * @param string $openAPIContent
     * @throws TypeErrorException|\Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException
     */
    public function __construct(string $openAPIContent)
    {
        $openAPIArray = json_decode($openAPIContent, true);
        if ($openAPIArray === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ResourceParseError('Cannot parse json content!');
        }

        parent::__construct($openAPIArray);
    }
}
