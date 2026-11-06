<?php

namespace Pelso\OpenAPIValidatorBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class ValidatorProfile
{
    /** @var string */
    public $profileName;
}