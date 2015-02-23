<?php

namespace Api\WebServiceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
#use Api\WebServiceBundle\DependencyInjection\Factory\SecurityFactory;

class ApiWebServiceBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $extension = $container->getExtension('security');
        // $extension->addSecurityListenerFactory(new SecurityFactory());
    }
}
