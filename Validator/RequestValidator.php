<?php

namespace Pelso\OpenAPIValidatorBundle\Validator;

use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Pelso\OpenAPIValidatorBundle\Exceptions\ValidationErrorException;
use Pelso\OpenAPIValidatorBundle\Provider\OpenAPIProviderInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

class RequestValidator implements RequestValidatorInterface
{

    public function validate(
        Request $request,
        OpenAPIProviderInterface $openAPIProvider
    ): void {
        try {
            $openAPIProvider
                ->getValidatorBuilder()
                ->getRequestValidator()
                ->validate(
                    $this->symfonyRequestToPsr7Request($request)
                );
        } catch (NoPath $exception) {
            return;
        } catch (ValidationFailed $exception) {
            throw new ValidationErrorException($exception);
        }

        return;
    }

    private function symfonyRequestToPsr7Request(Request $symfonyRequest): ServerRequest
    {
        $psr17Factory = new Psr17Factory();
        return (new PsrHttpFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        ))->createRequest($symfonyRequest);
    }
}
