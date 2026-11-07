<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Resolver;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;
use Pelso\OpenAPIValidatorBundle\Resolver\ValidatorProfileResolver;
use PHPUnit\Framework\TestCase;

class ValidatorProfileResolverTest extends TestCase
{
    /**
     * @dataProvider profileDataProvider
     *
     * @param array $configs
     * @return void
     *
     * @throws NonExistingValidatorProfileException
     */
    public function testInvalidProfileName(array $configs): void
    {
        $resolver = new ValidatorProfileResolver($configs);

        $this->expectException(NonExistingValidatorProfileException::class);

        $validatorProfile = new ValidatorProfile();
        $validatorProfile->profileName = 'profile_x2';
        $resolver->resolveAsValidator($validatorProfile);
    }

    /**
     * @dataProvider profileDataProvider
     *
     * @param array $configs
     * @return void
     *
     * @throws NonExistingValidatorProfileException
     */
    public function testValidatorValues(array $configs): void
    {
        $resolver = new ValidatorProfileResolver($configs);
        $validatorProfile = new ValidatorProfile();
        $validatorProfile->profileName = 'profile_x1';
        /** @var Validator $validator */
        $validator = $resolver->resolveAsValidator($validatorProfile);

        $this->assertEquals('provider1', $validator->providerName);
        $this->assertEquals(['@action1', '@action2'], $validator->errorActions);
    }

    public function profileDataProvider(): array
    {
        return [[[
            'profiles' => [[
                'profile_name' => 'profile_x1',
                'provider_name' => 'provider1',
                'error_actions' => [
                    '@action1',
                    '@action2',
                ],
            ]]
        ]]];
    }
}
