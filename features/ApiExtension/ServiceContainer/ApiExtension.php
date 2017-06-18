<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\ApiExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use NiR\GhDashboard\Behat\ApiExtension\Context\Initializer\HttpClientInitializer;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ApiExtension implements Extension
{
    public function getConfigKey()
    {
        return 'api';
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('base_url')->defaultValue('')->end()
            ->end()
        ;
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadHttpClient($container);
        $this->loadContextInitializer($container);

        $container->setParameter('api.base_url', $config['base_url']);
    }

    private function loadHttpClient(ContainerBuilder $container)
    {
        $container->setDefinition('api.http_client', new Definition(GuzzleClient::class));
        $container->setDefinition('api.http_adapter', new Definition(GuzzleAdapter::class, [
            new Reference('api.http_client'),
        ]));
        $container->setDefinition('api.request_factory', new Definition(GuzzleMessageFactory::class));
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(HttpClientInitializer::class, [
            new Reference('api.http_adapter'),
            new Reference('api.request_factory'),
            '%api.base_url%',
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

        $container->setDefinition('api.http_client_initializer', $definition);
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function process(ContainerBuilder $container)
    {
    }
}
