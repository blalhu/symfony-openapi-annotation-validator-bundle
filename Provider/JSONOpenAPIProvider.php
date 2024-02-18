<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;

class JSONOpenAPIProvider extends AbstractOpenAPIProvider implements OpenAPIProviderInterface
{
    /**
     * @param string $openAPIContent
     */
    public function __construct(string $openAPIContent)
    {
        $this->openAPIContent = $openAPIContent;
        $this->openAPIArray = json_decode($this->openAPIContent, true);
        if ($this->openAPIArray === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \ParseError('Cannot parse yaml content!');
        }
    }
}
