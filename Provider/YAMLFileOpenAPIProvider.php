<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YAMLFileOpenAPIProvider extends YAMLOpenAPIProvider implements OpenAPIProviderInterface
{
    use OpenAPIURILoaderTrait;

    /**
     * @param string $openAPIURL
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
