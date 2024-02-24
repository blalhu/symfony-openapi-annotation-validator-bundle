<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Collection;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Exceptions\ProviderNameAlreadyTakenException;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use PHPUnit\Framework\TestCase;

class OpenAPIProviderCollectionTest extends TestCase
{
    public function testDuplicationAvoidance(): void
    {
        $provider = new class implements OpenAPIProviderInterface
        {

            public function getArray(): array
            {
                return [];
            }

            public function getValidatorBuilder(): ValidatorBuilder
            {
                return new ValidatorBuilder();
            }
        };

        $this->expectException(ProviderNameAlreadyTakenException::class);
        (new OpenAPIProviderCollection())
            ->add('foo', $provider)
            ->add('foo', $provider);
    }
}
