<?php

namespace Pelso\OpenAPIValidatorBundle\Collection;

use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingProviderException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ProviderNameAlreadyTakenException;
use Pelso\OpenAPIValidatorBundle\Provider\JSONFileOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Pelso\OpenAPIValidatorBundle\Provider\YAMLFileOpenAPIProvider;
use phpDocumentor\Reflection\Types\This;

class OpenAPIProviderCollection implements OpenAPIProviderCollectionInterface
{
    /** @var OpenAPIProviderInterface[] */
    protected $providers = [];

    public function __construct(
        array $providerServiceList,
        array $yamlURIList,
        array $jsonURIList
    ) {
        foreach ($providerServiceList as $providerService) {
            $this->add(
                $providerService['name'],
                $providerService['service']
            );
        }
        foreach ($yamlURIList as $yamlURI) {
            $this->add(
                $yamlURI['name'],
                new YAMLFileOpenAPIProvider($yamlURI['uri'])
            );
        }
        foreach ($jsonURIList as $jsonURI) {
            $this->add(
                $jsonURI['name'],
                new JSONFileOpenAPIProvider($jsonURI['uri'])
            );
        }
    }

    public function add(
        string $name,
        OpenAPIProviderInterface $openAPIProvider
    ): OpenAPIProviderCollectionInterface {
        if (array_key_exists($name, $this->providers)) {
            throw new ProviderNameAlreadyTakenException();
        }

        $this->providers[$name] = $openAPIProvider;

        return $this;
    }

    public function get(string $name): OpenAPIProviderInterface
    {
        if (!array_key_exists($name, $this->providers)) {
            throw new NonExistingProviderException();
        }

        return $this->providers[$name];
    }

    public function getAll(): array
    {
        return $this->providers;
    }
}
