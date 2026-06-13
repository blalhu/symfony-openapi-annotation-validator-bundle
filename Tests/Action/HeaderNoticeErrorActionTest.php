<?php

namespace Pelso\OpenAPIValidatorBundle\Action;

use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class HeaderNoticeErrorActionTest extends TestCase
{
    private const EXCEPTION_TEXT = 'Literal test exception message.';

    public function testImplementation(): void
    {
        $this->assertTrue(
            in_array(
                ErrorActionInterface::class,
                class_implements(new HeaderNoticeErrorAction())
            )
        );
    }

    public function testHeaderContent(): void
    {
        $eventMock = $this->createMock(FilterControllerEvent::class);
        $eventMock->method('getRequest')->willReturn(new class
        {
            public $headers;

            public function __construct()
            {
                $this->headers = new class
                {
                    private $headers = [];

                    public function set(string $name, string $value)
                    {
                        $this->headers[$name] = $value;
                    }

                    public function get(string $name): string
                    {
                        return $this->headers[$name];
                    }
                };
            }
        });

        (new HeaderNoticeErrorAction())->triggerAction(
            new ValidationErrorException(
                new \Exception(
                    self::EXCEPTION_TEXT
                )
            ),
            $eventMock
        );

        $this->assertEquals(
            self::EXCEPTION_TEXT,
            $eventMock
                ->getRequest()
                ->headers
            ->get(HeaderNoticeErrorAction::HEADER_KEY)
        );
    }

    public function testSymfonyHeaderName(): void
    {
        $this->assertEquals('x-pelso-openapi-validation-error', HeaderNoticeErrorAction::getGeneratedHeaderKey());
    }
}
