<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotation;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorAnnotationInterface;
use PHPUnit\Framework\TestCase;

class ValidatorAnnotationTest extends TestCase
{
    /** @var ValidatorAnnotationInterface $annotation */
    private $loadedAnnotation;

    protected function setUp()
    {
        parent::setUp();

        $checker = new ValidatorAnnotationChecker();
        $reflectionClass = new \ReflectionClass($checker);
        $reader = new AnnotationReader();
        /** @var ValidatorAnnotationInterface $annotation */
        $this->loadedAnnotation = $reader->getClassAnnotation($reflectionClass, ValidatorAnnotation::class);
    }

    public function testAnnotationValuePass(): void
    {
        $this->assertEquals(
            2,
            count($this->loadedAnnotation->getErrorActions())
        );
    }

    public function testNameGetter(): void
    {
        $this->assertEquals(
            'provider_name',
            $this->loadedAnnotation->getProviderName()
        );
    }
}
