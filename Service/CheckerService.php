<?php

namespace Pelso\OpenAPIValidatorBundle\Service;

use Psr\Log\LoggerInterface;

class CheckerService
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function check()
    {
        $this->logger->warning('Third party bundle checker() method was called.');
    }
}
