<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
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
        $rootNode = $treeBuilder->root('pelso_open_api_validator_bundle');

        $rootNode
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
                ->arrayNode('annotations')
                    ->prototype('scalar')->end()
                    ->defaultValue([
                        Validator::class,
                        DefaultValidator::class,
                        ValidatorProfile::class
                    ])
                    ->validate()
                        ->ifTrue(function (array $annotations): bool {
                            foreach ($annotations as $annotation) {
                                if (
                                    !is_string($annotation)
                                    || (
                                        $annotation !== ValidatorProfile::class
                                        && !is_a($annotation, ValidatorAnnotationInterface::class, true)
                                    )
                                ){
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid('Annotations must implement ValidatorAnnotationInterface or ValidatorProfile!')
                    ->end()
                ->end()
                ->arrayNode('openapi_provider_list')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('service')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($value): bool {
                            return $this->arrayFieldUniquenessChecker(
                                'name',
                                $value
                            );
                        })
                        ->thenInvalid('Each openapi_provider_list.[].name must be unique.')
                    ->end()
                ->end()
                ->arrayNode('openapi_yaml_list')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('uri')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($value): bool {
                            return $this->arrayFieldUniquenessChecker(
                                'name',
                                $value
                            );
                        })
                        ->thenInvalid('Each openapi_yaml_list.[].name must be unique.')
                    ->end()
                ->end()
                ->arrayNode('openapi_json_list')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('uri')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($value): bool {
                            return $this->arrayFieldUniquenessChecker(
                                'name',
                                $value
                            );
                        })
                        ->thenInvalid('Each openapi_json_list.[].name must be unique.')
                    ->end()
                ->end()
                ->arrayNode('profiles')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('profile_name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('provider_name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('error_actions')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')
                                    ->cannotBeEmpty()
                                    ->validate()
                                        ->ifTrue(function ($value) {
                                            return !is_string($value);
                                        })
                                        ->thenInvalid('Each error_action must be a string.')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($value): bool {
                            return $this->arrayFieldUniquenessChecker(
                                'profile_name',
                                $value
                            );
                        })
                        ->thenInvalid('Each profile_name must be unique.')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function arrayFieldUniquenessChecker(
        string $fieldName,
        array $array
    ): bool {
        $names = array_column($array, $fieldName);

        return count($names) !== count(array_unique($names));
    }
}
