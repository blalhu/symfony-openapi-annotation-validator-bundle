<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Unit\DependencyInjection;

use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidator;
use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
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

    public function testDuplicatedNamesAreRejectedAcrossProviderTypes(): void
    {
        $yamlProvider = [
            'name' => 'name1',
            'uri' => '/foo/bar',
        ];
        $provider = [
            'name' => 'name1',
            'service' => '@service.name',
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Names must be unique across openapi_provider_list, openapi_yaml_list and openapi_json_list.');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [
                ['openapi_yaml_list' => [$yamlProvider]],
                ['openapi_provider_list' => [$provider]],
            ]
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


    /**
     * @param $annotation
     *
     * @return void
     *
     * @dataProvider invalidAnnotationDataProvider
     */
    public function testInvalidAnnotations($annotation): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Annotations must implement ValidatorAnnotationInterface or ValidatorProfile!');

        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['annotations' => [$annotation]]]
        );
    }

    public function invalidAnnotationDataProvider(): array
    {
        return [
            [null],
            [1],
            [1.1],
            [\stdClass::class],
            ['some string'],
        ];
    }

    /**
     * @param $annotation
     *
     * @return void
     *
     * @dataProvider validAnnotationDataProvider
     */
    public function testValidAnnotations($annotation): void
    {
        (new Processor())->processConfiguration(
            new BundleConfiguration(),
            [['annotations' => [$annotation]]]
        );

        $this->assertTrue(true);
    }

    public function validAnnotationDataProvider(): array
    {
        return [
            [DefaultValidator::class],
            [Validator::class],
            [ValidatorProfile::class],
        ];
    }
}
