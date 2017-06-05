<?php

declare(strict_types=1);

namespace NiR\GhDashboard;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface
{
    private $container;

    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger    = $logger;
    }

    public function getController(Request $request)
    {
        if (!$request->attributes->has('_action') || !$request->attributes->has('_responder')) {
            $this->logger->warning('Unable to resolve the controller as the "_action" and/or "_responder" parameters are missing.');
            return false;
        }

        $actionName    = $request->attributes->get('_action');
        $responderName = $request->attributes->get('_responder');

        return function (Request $request) use ($actionName, $responderName): Response {
            $action    = $this->container->get($actionName);
            $responder = $this->container->get($responderName);

            return $responder($action($request));
        };
    }

    public function getArguments(Request $request, $controller)
    {
        // not used
        return [];
    }
}
