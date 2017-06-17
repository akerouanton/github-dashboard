<?php

namespace spec\NiR\GhDashboard\Symfony;

use NiR\GhDashboard\Symfony\ControllerResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolverSpec extends ObjectBehavior
{
    function let(Container $container, LoggerInterface $logger)
    {
        $this->beConstructedWith($container, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ControllerResolver::class);
    }

    function it_is_a_controller_resolver()
    {
        $this->shouldImplement(ControllerResolverInterface::class);
    }

    function it_returns_false_if_action_is_missing_from_request_attributes(
        Request $request,
        ParameterBag $attributes,
        $logger
    ) {
        $request->attributes = $attributes;

        $attributes->has('_action')->willReturn(false);
        $attributes->has('_responder')->willReturn(true);
        $logger->warning(Argument::type('string'));

        $this->getController($request)->shouldReturn(false);
    }

    function it_returns_false_if_responder_is_missing_from_request_attributes(
        Request $request,
        ParameterBag $attributes,
        $logger
    ) {
        $request->attributes = $attributes;

        $attributes->has('_action')->willReturn(true);
        $attributes->has('_responder')->willReturn(false);
        $logger->warning(Argument::type('string'));

        $this->getController($request)->shouldReturn(false);
    }

    function it_resolves_a_controller_based_on_action_and_responder_request_attributes(
        Request $request,
        ParameterBag $attributes
    ) {
        $request->attributes = $attributes;

        $attributes->has('_action')->willReturn(true);
        $attributes->has('_responder')->willReturn(true);
        $attributes->get('_action')->willReturn('MyAction');
        $attributes->get('_responder')->willReturn('MyResponder');

        $this->getController($request)->shouldHaveType('closure');
    }
}
