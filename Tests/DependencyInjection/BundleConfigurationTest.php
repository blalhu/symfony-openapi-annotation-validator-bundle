<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\DependencyInjection\BundleConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class BundleConfigurationTest extends TestCase
{
    /**
     * @dataProvider invalidErrorActions
     */
    public function testErrorActionTypeValidator($value): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [
                [
                    'profiles' => [
                        [
                            'profile_name' => 'strict',
                            'provider_name' => 'default',
                            'error_actions' => [$value],
                        ],
                    ],
                ],
            ]
        );
    }

    public function invalidErrorActions(): array
    {
        return [
            'integer' => [123],
            'float' => [1.5],
            'boolean' => [true],
            'null' => [null],
            'empty string' => [''],
            'array' => [[]],
        ];
    }

    public function testDuplicateProviderNamesAreRejected(): void
    {
        $provider = [
            'name' => 'name1',
            'service' => '@service.name',
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Each openapi_provider_list.[].name must be unique.');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['openapi_provider_list' => [$provider, $provider]]]
        );
    }

    public function testDuplicateYAMLNamesAreRejected(): void
    {
        $provider = [
            'name' => 'name1',
            'uri' => '/foo/bar',
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Each openapi_yaml_list.[].name must be unique.');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['openapi_yaml_list' => [$provider, $provider]]]
        );
    }

    public function testDuplicateJSONNamesAreRejected(): void
    {
        $provider = [
            'name' => 'name1',
            'uri' => '/foo/bar',
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Each openapi_json_list.[].name must be unique.');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['openapi_json_list' => [$provider, $provider]]]
        );
    }

    public function testDuplicateProfileNamesAreRejected(): void
    {
        $profile = [
            'profile_name' => 'strict',
            'provider_name' => 'default',
            'error_actions' => ['@app.reject'],
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Each profile_name must be unique.');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['profiles' => [$profile, $profile]]]
        );
    }
}
