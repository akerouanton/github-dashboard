<?php

namespace spec\NiR\GhDashboard\Symfony;

use NiR\GhDashboard\Symfony\ExceptionListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListenerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExceptionListener::class);
    }

    function it_creates_a_response_with_status_code_and_headers_from_exception_if_it_has_a_status_code_below_500(
        GetResponseForExceptionEvent $event,
        HttpExceptionInterface $exception
    ) {
        $event->getException()->willReturn($exception);
        $exception->getStatusCode()->willReturn(405);
        $exception->getHeaders()->willReturn(['X-Custom-Header' => 'yolo']);
        $event->setResponse(Argument::exact(new Response('', 405, ['X-Custom-Header' => 'yolo'])))->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->onKernelException($event);
    }

    function it_creates_a_500_response_and_logs_the_exception_when_it_is_not_a_http_exception(
        GetResponseForExceptionEvent $event,
        Request $request,
        $logger
    ) {
        $event->getException()->willReturn(new \RuntimeException());
        $event->getRequest()->willReturn($request);
        $logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $event->setResponse(Argument::exact(new Response('', 500)))->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->onKernelException($event);
    }

    function it_creates_a_500_response_and_logs_the_exception_when_it_has_a_status_code_above_or_equal_500(
        GetResponseForExceptionEvent $event,
        HttpExceptionInterface $exception,
        Request $request,
        $logger
    ) {
        $event->getException()->willReturn($exception);
        $exception->getStatusCode()->willReturn(504);
        $exception->getHeaders()->willReturn(['X-Custom-Header' => 'yolo']);
        $event->getRequest()->willReturn($request);
        $logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $event->setResponse(Argument::exact(new Response('', 500)))->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->onKernelException($event);
    }
}
