<?php

namespace Pelso\OpenAPIValidatorBundle\Collection;

use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;

interface OpenAPIProviderCollectionInterface
{
    public function add(
        string $name,
        OpenAPIProviderInterface $openAPIProvider
    ): OpenAPIProviderCollectionInterface;

    public function get(string $name): OpenAPIProviderInterface;

    public function getAll(): array;
}
