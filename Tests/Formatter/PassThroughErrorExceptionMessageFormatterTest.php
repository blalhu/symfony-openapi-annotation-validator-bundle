<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Formatter;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Formatter\PassThroughErrorExceptionMessageFormatter;
use PHPUnit\Framework\TestCase;

class PassThroughErrorExceptionMessageFormatterTest extends TestCase
{
    public function testMessageSource(): void
    {

        $formattedMessage = (new PassThroughErrorExceptionMessageFormatter())->format(
            new ValidationErrorException(
                new \Exception('original_message'),
                'validation_message'
            )
        );

        $this->assertEquals('original_message', $formattedMessage);
    }
}
