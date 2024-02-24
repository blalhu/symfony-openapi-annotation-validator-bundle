<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use cebe\openapi\exceptions\TypeErrorException;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\Yaml\Yaml;

class JSONFileOpenAPIProvider extends JSONOpenAPIProvider implements OpenAPIProviderInterface
{
    use OpenAPIURILoaderTrait;

    /**
     * @param string $openAPIURL
     * @throws TypeErrorException|\Pelso\OpenAPIValidatorBundle\Exceptions\InvalidOpenAPISchemeException
     */
    public function __construct(string $openAPIURL)
    {
        parent::__construct(
            $this->loadOpenApiURIResource(
                $openAPIURL
            )
        );
    }
}
