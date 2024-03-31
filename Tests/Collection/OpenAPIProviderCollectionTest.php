<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Collection;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Collection\OpenAPIProviderCollection;
use Pelso\OpenAPIValidatorBundle\Exceptions\NonExistingProviderException;
use Pelso\OpenAPIValidatorBundle\Exceptions\ProviderNameAlreadyTakenException;
use Pelso\OpenAPIValidatorBundle\Provider\JSONFileOpenAPIProvider;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Pelso\OpenAPIValidatorBundle\Provider\YAMLFileOpenAPIProvider;
use PHPUnit\Framework\TestCase;

class OpenAPIProviderCollectionTest extends TestCase
{
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new class implements OpenAPIProviderInterface
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
    }

    public function testDuplicationAvoidance(): void
    {
        $this->expectException(ProviderNameAlreadyTakenException::class);
        (new OpenAPIProviderCollection([], [], []))
            ->add('foo', $this->provider)
            ->add('foo', $this->provider);
    }

    public function testGetter(): void
    {
        $collection = (new OpenAPIProviderCollection([], [], []))
            ->add('p1', $this->provider)
            ->add('p2', $this->provider);

        $this->assertEquals($this->provider, $collection->get('p1'));
    }

    public function testNonExistingProvider(): void
    {
        $collection = (new OpenAPIProviderCollection([], [], []))
            ->add('p1', $this->provider);

        $this->expectException(NonExistingProviderException::class);
        $collection->get('p2');
    }

    public function testAllGetter(): void
    {
        $collection = (new OpenAPIProviderCollection([], [], []))
            ->add('p1', $this->provider)
            ->add('p2', $this->provider);

        $this->assertEquals(2, count($collection->getAll()));
    }

    public function testConstructor(): void
    {
        $collection = new OpenAPIProviderCollection(
            [['name' => 'foo1', 'service' => new YAMLFileOpenAPIProvider(__DIR__.'/../resources/valid_openapi.yaml')]],
            [['name' => 'foo2', 'uri' => __DIR__.'/../resources/valid_openapi.yaml']],
            [['name' => 'foo3', 'uri' => __DIR__.'/../resources/valid_openapi.json']]
        );

        $this->assertEquals(3, count($collection->getAll()));
    }
}
