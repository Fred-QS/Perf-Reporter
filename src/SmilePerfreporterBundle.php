<?php

namespace Smile\PerfreporterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Smile\PerfreporterBundle\DependencyInjection\PerfreporterExtension;

class SmilePerfreporterBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $ext = new PerfreporterExtension([],$container);

    }
}