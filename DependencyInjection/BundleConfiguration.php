<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BundleConfiguration implements ConfigurationInterface
{
    const REQUEST_INTERCEPTOR = 'pelso.openapi_validator_bundle.request_interceptor';
    const VALIDATOR = 'pelso.openapi_validator_bundle.validator';
    const PROVIDER_COLLECTION = 'pelso.openapi_validator_bundle.provider_collection';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pelso');

        $rootNode
            ->children()
                ->arrayNode('openapi_validator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('interceptor')
                            ->defaultValue('@' . self::REQUEST_INTERCEPTOR)
                        ->end()
                        ->scalarNode('validator')
                            ->defaultValue('@' . self::VALIDATOR)
                        ->end()
                        ->scalarNode('definition_provider_collection')
                            ->defaultValue('@' . self::PROVIDER_COLLECTION)
                        ->end()
                        ->arrayNode('openapi_provider_list')
                            ->defaultValue([])
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('service')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('openapi_yaml_list')
                            ->defaultValue([])
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('uri')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('openapi_json_list')
                            ->defaultValue([])
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('uri')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
