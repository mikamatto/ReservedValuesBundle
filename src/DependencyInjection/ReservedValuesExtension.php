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

        // Set bypass roles parameter
        $container->setParameter('reserved_values.bypass_roles', $config['bypass_roles']);

        // Set the restricted values parameters
        if (isset($config['keys'])) {
            foreach ($config['keys'] as $key => $values) {
                $container->setParameter("reserved_values.keys.$key.exact", $values['exact'] ?? []);
                $container->setParameter("reserved_values.keys.$key.patterns", $values['patterns'] ?? []);
            }
        }
    }
}