<?php

namespace Mikamatto\ReservedValuesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ReservedValuesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load the services configuration from the bundle
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Process the user-provided configuration, if available
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set the restricted values parameter directly
        foreach ($config as $key => $values) {
            $container->setParameter("reserved_values.$key.exact", $values['exact'] ?? []);
            $container->setParameter("reserved_values.$key.patterns", $values['patterns'] ?? []);
        }
    }
}