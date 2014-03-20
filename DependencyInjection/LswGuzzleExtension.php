<?php

namespace Lsw\GuzzleBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Applies the configuration for the Guzzle object
 */
class LswGuzzleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.yml');
        }

        if (isset($config['clients'])) {
            $this->addClients($config['clients'], $container);
        }
    }

    /**
     * Adds Guzzle clients to the service contaienr
     *
     * @param array            $clients   Array of client configurations
     * @param ContainerBuilder $container Service container
     *
     * @throws \LogicException
     */
    private function addClients(array $clients, ContainerBuilder $container)
    {
        foreach ($clients as $client => $clientConfig) {
        	$this->newGuzzleClient($client, $clientConfig, $container);
        }
    }

    /**
     * Creates a new Guzzle client definition
     *
     * @param string           $name      Client name
     * @param array            $config    Client configuration
     * @param ContainerBuilder $container Service container
     *
     * @throws \LogicException
     */
    private function newGuzzleClient($name, array $config, ContainerBuilder $container)
    {
        $client = new Definition('Guzzle\Service\Description\ServiceDescription');
        $client->setFactoryClass('Guzzle\Service\Description\ServiceDescription');
        $client->setFactoryMethod('factory');
        if (!isset($config['description']['name'])) {
        	$config['description']['name'] = $name;
        }
        $client->addArgument($config['description']);
        // Add the service to the container
        $serviceName = sprintf('lsw_guzzle.%s', $name);
        $container->setDefinition($serviceName, $client);
        
        $client = new Definition('Guzzle\Service\Client');
        $client->setFactoryClass('Guzzle\Service\Client');
        $client->setFactoryMethod('factory');
        $client->addArgument($config['config']);
        $client->addMethodCall('setDescription', array(new Reference($serviceName)));
        if ($container->hasDefinition('lsw_guzzle.command_history_plugin')) {
        	$client->addMethodCall('addSubscriber',array(new Reference('lsw_guzzle.command_history_plugin')));
        }
        // Add the service to the container
        $serviceName = sprintf('guzzle.%s', $name);
        $container->setDefinition($serviceName, $client);
    }

}
