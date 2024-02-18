<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YAMLOpenAPIProvider extends AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /**
     * @param string $openAPIContent
     */
    public function __construct(string $openAPIContent)
    {
        $this->openAPIContent = $openAPIContent;
        $this->openAPIArray = Yaml::parse($this->openAPIContent);
    }
}
