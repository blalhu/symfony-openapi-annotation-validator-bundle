<?php

namespace Pelso\OpenAPIValidatorBundle;

use Pelso\OpenAPIValidatorBundle\DependencyInjection\PelsoOpenAPIValidatorBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PelsoOpenAPIValidatorBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PelsoOpenAPIValidatorBundleExtension();
    }
}
