<?php

namespace Pelso\OpenAPIValidatorBundle\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Action\BadRequestResponseErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\ExceptionErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\HeaderNoticeErrorAction;
use Pelso\OpenAPIValidatorBundle\Action\LogErrorAction;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Interceptor\RequestInterceptorService;
use Pelso\OpenAPIValidatorBundle\Validator\RequestValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class PelsoOpenAPIValidatorBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new BundleConfiguration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pelso_open_api_validator_bundle.openapi_provider_list', $configs['openapi_provider_list']);
        $container->setParameter('pelso_open_api_validator_bundle.openapi_yaml_list', $configs['openapi_yaml_list']);
        $container->setParameter('pelso_open_api_validator_bundle.openapi_json_list', $configs['openapi_json_list']);

        $container = $this->defineValidators($configs, $container);

        $container = $this->defineErrorActions($configs, $container);

        $container = $this->defineRequestInterceptor($configs, $container);
        $container = $this->defineValidator($configs, $container);
        $container = $this->defineProviderCollection($configs, $container);
    }

    private function defineErrorActions(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        $container
            ->register('pelso.openapi_validator_bundle.error_action.bad_request_response')
            ->setClass(BadRequestResponseErrorAction::class)
            ->addTag('pelso.error_action');

        $container
            ->register('pelso.openapi_validator_bundle.error_action.exception')
            ->setClass(ExceptionErrorAction::class)
            ->addTag('pelso.error_action');

        $container
            ->register('pelso.openapi_validator_bundle.error_action.header_notice')
            ->setClass(HeaderNoticeErrorAction::class)
            ->addTag('pelso.error_action');

        $container
            ->register('pelso.openapi_validator_bundle.error_action.log')
            ->setClass(LogErrorAction::class)
            ->addTag('pelso.error_action');

        return $container;
    }

    private function defineValidators(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        $container
            ->register('pelso.openapi_validator_bundle.annotation.validator')
            ->setClass(ValidatorAnnotation::class)
            ->addTag('pelso.validator_annotation');

        $container
            ->register('pelso.openapi_validator_bundle.annotation.default_validator')
            ->setClass(DefaultValidatorAnnotation::class)
            ->addTag('pelso.validator_annotation');

        return $container;
    }

    private function defineRequestInterceptor(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['interceptor'] !== '@' . BundleConfiguration::REQUEST_INTERCEPTOR) {
            $container->setAlias(
                BundleConfiguration::REQUEST_INTERCEPTOR,
                preg_replace('/^\@/', '', $configs['interceptor'])
            );
        } else {
            $errorActionServiceIds = array_keys($container->findTaggedServiceIds('pelso.error_action'));
            $container
                ->register(BundleConfiguration::REQUEST_INTERCEPTOR)
                ->setClass(RequestInterceptorService::class)
                ->addArgument(new Reference(BundleConfiguration::VALIDATOR))
                ->addArgument(new Reference(BundleConfiguration::PROVIDER_COLLECTION))
                ->addArgument(array_map(
                    function (string $serviceId) use ($container) {
                        return new Reference($serviceId);
                    },
                    array_keys($container->findTaggedServiceIds('pelso.validator_annotation'))
                ))
                ->addArgument(array_combine(
                    array_map(
                        function (string $serviceId): string {
                            return '@'.$serviceId;
                        },
                        $errorActionServiceIds
                    ),
                    array_map(
                        function (string $serviceId) use ($container) {
                            return new Reference($serviceId);
                        },
                        $errorActionServiceIds
                    )
                ))
                ->addTag('kernel.event_listener', ['event' => 'kernel.controller']);
        }

        return $container;
    }

    private function defineValidator(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['validator'] !== '@' . BundleConfiguration::VALIDATOR) {
            $container->setAlias(
                BundleConfiguration::VALIDATOR,
                preg_replace('/^\@/', '', $configs['validator'])
            );
        } else {
            $container
                ->register(BundleConfiguration::VALIDATOR)
                ->setClass(RequestValidator::class);
        }

        return $container;
    }

    private function defineProviderCollection(array $configs, ContainerBuilder $container): ContainerBuilder
    {
        if ($configs['definition_provider_collection'] !== '@' . BundleConfiguration::PROVIDER_COLLECTION) {
            $container->setAlias(
                BundleConfiguration::PROVIDER_COLLECTION,
                preg_replace('/^\@/', '', $configs['definition_provider_collection'])
            );
        } else {
            $container
                ->register(BundleConfiguration::PROVIDER_COLLECTION)
                ->setClass(OpenAPIProviderCollection::class)
                ->addArgument(
                    array_map(
                        function (array $serviceDefinition): array {
                            $serviceDefinition['service'] = new Reference(
                                ltrim(
                                    $serviceDefinition['service'],
                                    '@'
                                )
                            );

                            return $serviceDefinition;
                        },
                        $container->getParameter('pelso_open_api_validator_bundle.openapi_provider_list')
                    )
                )
                ->addArgument('%pelso_open_api_validator_bundle.openapi_yaml_list%')
                ->addArgument('%pelso_open_api_validator_bundle.openapi_json_list%');
        }

        return $container;
    }
}
