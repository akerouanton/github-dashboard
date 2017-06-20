<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\RequestValidator;
use NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent\SignatureChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RequestValidatorSpec extends ObjectBehavior
{
    function let(SignatureChecker $signatureChecker, LoggerInterface $logger)
    {
        $this->beConstructedWith($signatureChecker, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestValidator::class);
    }

    function it_invalidates_a_request_when_headers_does_not_contain_signature(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(false);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_headers_does_not_contain_delivery_id(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(false);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_headers_does_not_contain_event_type(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(false);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_the_payload_is_empty(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(false);

        $this->validate($request, '')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_signature_does_not_contain_any_equal_separator_between_algo_and_hash(
        ServerRequestInterface $request
    ) {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $request->getHeader('X-Hub-Signature')->willReturn(['hmac_digest']);
        $request->getHeader('X-GitHub-Delivery')->willReturn(['c829d500-499c-11e7-9fc5-ea52b89e9429']);
        $request->getHeader('X-GitHub-Event')->willReturn(['ping']);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_the_delivery_id_is_empty(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $request->getHeader('X-Hub-Signature')->willReturn(['sha1=hmac_digest']);
        $request->getHeader('X-GitHub-Delivery')->willReturn(['']);
        $request->getHeader('X-GitHub-Event')->willReturn(['ping']);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_the_event_type_is_empty(ServerRequestInterface $request)
    {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $request->getHeader('X-Hub-Signature')->willReturn(['sha1=hmac_digest']);
        $request->getHeader('X-GitHub-Delivery')->willReturn(['c829d500-499c-11e7-9fc5-ea52b89e9429']);
        $request->getHeader('X-GitHub-Event')->willReturn(['']);

        $this->validate($request, 'payload')->shouldReturn(false);
    }

    function it_invalidates_a_request_when_signature_is_not_valid(
        ServerRequestInterface $request,
        $signatureChecker,
        $logger
    ) {
        $request->hasHeader('X-Hub-Signature')->willReturn(true);
        $request->hasHeader('X-GitHub-Delivery')->willReturn(true);
        $request->hasHeader('X-GitHub-Event')->willReturn(true);

        $request->getHeader('X-Hub-Signature')->willReturn(['sha1=hmac_digest']);
        $request->getHeader('X-GitHub-Delivery')->willReturn(['c829d500-499c-11e7-9fc5-ea52b89e9429']);
        $request->getHeader('X-GitHub-Event')->willReturn(['ping']);

        $signatureChecker
            ->validate('hmac_digest', '{"repository":{"full_name":"NiR/github-dashboard"}}')
            ->willReturn(false)
        ;

        $logger->info(
            Argument::type('string'),
            Argument::exact(['payload' => '{"repository":{"full_name":"NiR/github-dashboard"}}', 'signature' => 'sha1=hmac_digest'])
        )->shouldBeCalled();

        $this
            ->validate($request, '{"repository":{"full_name":"NiR/github-dashboard"}}')
            ->shouldReturn(false)
        ;
    }
}
