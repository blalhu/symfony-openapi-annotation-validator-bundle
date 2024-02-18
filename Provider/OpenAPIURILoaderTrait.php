<?php

namespace Pelso\OpenAPIValidatorBundle\Provider;

trait OpenAPIURILoaderTrait
{
    /**
     * @param string $fileURI
     * @return string
     */
    private function loadOpenApiURIResource(string $fileURI): string
    {
        $openAPIContent = @file_get_contents($fileURI);
        if (!$openAPIContent) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot read file with given URI (%s)',
                $fileURI
            ));
        }

        return $openAPIContent;
    }
}