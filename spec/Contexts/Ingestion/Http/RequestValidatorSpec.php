<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http;

use NiR\GhDashboard\Contexts\Ingestion\Http\RequestValidator;
use NiR\GhDashboard\Contexts\Ingestion\Http\SignatureChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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

    function it_invalidates_a_request_when_headers_does_not_contain_signature(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(false);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(true);

        $this->validate($request)->shouldReturn(false);
    }

    function it_invalidates_a_request_when_headers_does_not_contain_delivery_id(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(false);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(true);

        $this->validate($request)->shouldReturn(false);
    }

    function it_invalidates_a_request_when_headers_does_not_contain_event_type(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(false);
        $requestBag->has('payload')->willReturn(true);

        $this->validate($request)->shouldReturn(false);
    }

    function it_invalidates_a_request_when_it_does_not_contain_payload(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(false);

        $this->validate($request)->shouldReturn(false);
    }

    function it_invalidates_a_request_when_signature_is_not_valid(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag,
        $signatureChecker,
        $logger
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(true);
        $headersBag->get('X-Hub-Signature')->willReturn('hmac_digest');
        $headersBag->get('X-Github-Delivery')->willReturn('c829d500-499c-11e7-9fc5-ea52b89e9429');
        $headersBag->get('X-Github-Event')->willReturn('ping');
        $requestBag->get('payload')->willReturn('{"repository":{"full_name":"NiR/github-dashboard"}}');

        $signatureChecker
            ->validate('hmac_digest', '{"repository":{"full_name":"NiR/github-dashboard"}}')
            ->willReturn(false)
        ;

        $logger->info(
            Argument::type('string'),
            Argument::exact(['payload' => '{"repository":{"full_name":"NiR/github-dashboard"}}', 'signature' => 'hmac_digest'])
        )->shouldBeCalled();

        $this->validate($request)->shouldReturn(false);
    }
}
