<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\WebServerBundle\WebServerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new MonologBundle(),
            new DoctrineBundle(),
            new SensioFrameworkExtraBundle(),
        ];

        if ($this->environment === 'dev') {
            $bundles[] = new WebServerBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader = new YamlFileLoader($c, new FileLocator($this, __DIR__ . '/../../config'));
        $loader->load('services.yml');
        $loader->load('config.yml');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $ingestEvent = (new Route(
            '/hook',
            ['_action' => IngestEvent\Action::class, '_responder' => IngestEvent\Responder::class]
        ))->setMethods(['POST']);

        $routes->addRoute($ingestEvent);
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../var/logs';
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../var/cache';
    }
}
