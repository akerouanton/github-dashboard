<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Action;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\Response;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\RequestValidator;
use NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent as UseCase;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ActionSpec extends ObjectBehavior
{
    function let(RequestValidator $validator, UseCase\UseCase $useCase, LoggerInterface $logger)
    {
        $this->beConstructedWith($validator, $useCase, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Action::class);
    }

    function it_returns_a_failed_response_when_json_payload_is_not_decodable(
        ServerRequestInterface $request,
        StreamInterface $body,
        $validator
    ) {
        $request->getBody()->willReturn($body);
        $body->__toString()->willReturn('{"repository"');

        $validator->validate($request, '{"repository"')->willReturn(true);

        $this->__invoke($request)->shouldBeLike(Response::failed());
    }

    function it_returns_a_failed_response_when_json_payload_does_not_contain_repository_name(
        ServerRequestInterface $request,
        StreamInterface $body,
        $validator
    ) {
        $request->getBody()->willReturn($body);
        $body->__toString()->willReturn('{}');

        $validator->validate($request, '{}')->willReturn(true);

        $this->__invoke($request)->shouldBeLike(Response::failed());
    }

    function it_returns_a_successful_response_when_use_case_succeed(
        ServerRequestInterface $request,
        StreamInterface $body,
        UseCase\Response $response,
        $validator,
        $useCase
    ) {
        $request->getBody()->willReturn($body);
        $body->__toString()->willReturn('{"repository":{"full_name":"NiR/github-dashboard"}}');

        $validator
            ->validate($request, '{"repository":{"full_name":"NiR/github-dashboard"}}')
            ->willReturn(true)
        ;

        $request->getHeader('X-GitHub-Delivery')->willReturn(['c829d500-499c-11e7-9fc5-ea52b89e9429']);
        $request->getHeader('X-GitHub-Event')->willReturn(['ping']);

        $useCaseRequeset = new UseCase\Request(
            'c829d500-499c-11e7-9fc5-ea52b89e9429',
            'NiR/github-dashboard',
            'ping',
            ['repository' => ['full_name' => 'NiR/github-dashboard']]
        );

        $useCase
            ->__invoke(Argument::exact($useCaseRequeset))
            ->shouldBeCalled()
            ->willReturn($response);
        ;

        $response->isSuccessful()->willReturn(true);

        $this->__invoke($request)->shouldBeLike(Response::succeed());
    }
}
