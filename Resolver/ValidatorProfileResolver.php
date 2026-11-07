<?php

namespace Pelso\OpenAPIValidatorBundle\Resolver;

use Pelso\OpenAPIValidatorBundle\Annotation\Validator;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingValidatorProfileException;

class ValidatorProfileResolver
{
    /** @var array $configs */
    private $configs;

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @throws NonExistingValidatorProfileException
     */
    public function resolveAsValidator(
        ValidatorProfile $validatorProfile
    ): ValidatorAnnotationInterface {
        $profileConfig = [];
        foreach ($this->configs['profiles'] as $profile) {
            if ($profile['profile_name'] === $validatorProfile->profileName) {
                $profileConfig = $profile;
            }
        }
        if (empty($profileConfig)) {
            throw new NonExistingValidatorProfileException();
        }

        $annotation = new Validator();
        $annotation->providerName = $profileConfig['provider_name'];
        $annotation->errorActions = $profileConfig['error_actions'];

        return $annotation;
    }
}
