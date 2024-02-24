<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use cebe\openapi\exceptions\TypeErrorException;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YAMLOpenAPIProvider extends AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /**
     * @param string $openAPIContent
     * @throws TypeErrorException|\Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException
     */
    public function __construct(string $openAPIContent)
    {
        parent::__construct(
            Yaml::parse(
                $openAPIContent
            )
        );
    }
}
