<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Pelso\OpenAPIValidatorBundle\Annotation\DefaultValidatorAnnotation;
use PHPUnit\Framework\TestCase;

class DefaultValidatorAnnotationTest extends TestCase
{
    /** @var ValidatorAnnotationInterface $annotation */
    private $loadedAnnotation;

    protected function setUp()
    {
        parent::setUp();

        $checker = new DefaultValidatorAnnotationChecker();
        $reflectionClass = new \ReflectionClass($checker);
        $reader = new AnnotationReader();
        /** @var ValidatorAnnotationInterface $annotation */
        $this->loadedAnnotation = $reader->getClassAnnotation($reflectionClass, DefaultValidatorAnnotation::class);
    }

    public function testAnnotationValuePass(): void
    {
        $this->assertEquals(
            1,
            count($this->loadedAnnotation->getErrorActions())
        );
    }

    public function testNameGetter(): void
    {
        $this->assertEquals(
            'default',
            $this->loadedAnnotation->getProviderName()
        );
    }
}
