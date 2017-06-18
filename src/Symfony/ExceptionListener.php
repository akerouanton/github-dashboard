<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Symfony;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            $event->setResponse(new Response('', $exception->getStatusCode(), $exception->getHeaders()));
            $event->stopPropagation();
            return;
        }

        $this->logger->error('Exception thrown during request processing, returns a 500 response.', [
            'request' => $event->getRequest(),
            'exception' => $exception,
        ]);

        $event->setResponse(new Response('', 500));
        $event->stopPropagation();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [['onKernelException']],
        ];
    }
}
