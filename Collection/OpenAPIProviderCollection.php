<?php

namespace Pelso\OpenAPIValidatorBundle\Collection;

use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollectionInterface;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingProviderException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ProviderNameAlreadyTakenException;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use phpDocumentor\Reflection\Types\This;

class OpenAPIProviderCollection implements OpenAPIProviderCollectionInterface
{
    /** @var OpenAPIProviderInterface[] */
    protected $providers = [];

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
