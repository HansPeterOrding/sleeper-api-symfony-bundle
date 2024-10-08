<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HpoSleeperApiSymfonyExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__) . '/../config')
        );
        $loader->load('services.yaml');
    }
}
