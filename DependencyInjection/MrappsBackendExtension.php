<?php

namespace Mrapps\BackendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MrappsBackendExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('mrapps_backend.temp_folder', $config['temp_folder']);
        $container->setParameter('mrapps_backend.default_route_name', $config['default_route_name']);
        $container->setParameter('mrapps_backend.file_accepted_types.image', $config['file_accepted_types']['image']);
        $container->setParameter('mrapps_backend.file_accepted_types.video', $config['file_accepted_types']['video']);
        $container->setParameter('mrapps_backend.file_accepted_types.pdf', $config['file_accepted_types']['pdf']);
        $container->setParameter('mrapps_backend.file_accepted_types.zip', $config['file_accepted_types']['zip']);
        $container->setParameter('mrapps_backend.file_accepted_types.json', $config['file_accepted_types']['json']);
        $container->setParameter('mrapps_backend.sidebar_menu', $config['sidebar_menu']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
