<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use cebe\openapi\exceptions\TypeErrorException;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Exceptions\ResourceParseError;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YAMLOpenAPIProvider extends AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /**
     * @param string $openAPIContent
     * @throws TypeErrorException|\Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException
     */
    public function __construct(string $openAPIContent)
    {
        try {
            parent::__construct(
                Yaml::parse(
                    $openAPIContent
                )
            );
        } catch (ParseException $exception) {
            throw new ResourceParseError(
                'Cannot parse yaml content!',
                $exception->getCode(),
                $exception
            );
        }
    }
}
