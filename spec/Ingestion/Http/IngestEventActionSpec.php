<?php

namespace spec\NiR\GhDashboard\Ingestion\Http;

use NiR\GhDashboard\Ingestion\Http\IngestEventAction;
use NiR\GhDashboard\Ingestion\Http\IngestEventResponse;
use NiR\GhDashboard\Ingestion\Http\SignatureChecker;
use NiR\GhDashboard\Ingestion\UseCases\IngestEvent as UseCase;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class IngestEventActionSpec extends ObjectBehavior
{
    function let(UseCase\UseCase $useCase, LoggerInterface $logger, SignatureChecker $signatureChecker)
    {
        $this->beConstructedWith($useCase, $logger, $signatureChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IngestEventAction::class);
    }

    function it_returns_a_failed_response_when_request_headers_does_not_contain_signature(
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

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_request_headers_does_not_contain_delivery_id(
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

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_request_headers_does_not_contain_event_type(
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

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_request_headers_does_not_contain_payload(
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

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_signature_is_not_valid(
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

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_json_payload_is_not_decodable(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag,
        $signatureChecker
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(false);
        $headersBag->get('X-Hub-Signature')->willReturn('hmac_digest');
        $headersBag->get('X-Github-Delivery')->willReturn('c829d500-499c-11e7-9fc5-ea52b89e9429');
        $headersBag->get('X-Github-Event')->willReturn('ping');
        $requestBag->get('payload')->willReturn('{"repository"');

        $signatureChecker->validate('hmac_digest', '{"repository"')->willReturn(true);

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_json_payload_does_not_contain_repository_name(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag,
        $signatureChecker
    ) {
        $request->headers = $headersBag;
        $request->request = $requestBag;

        $headersBag->has('X-Hub-Signature')->willReturn(true);
        $headersBag->has('X-Github-Delivery')->willReturn(true);
        $headersBag->has('X-Github-Event')->willReturn(true);
        $requestBag->has('payload')->willReturn(false);
        $headersBag->get('X-Hub-Signature')->willReturn('hmac_digest');
        $headersBag->get('X-Github-Delivery')->willReturn('c829d500-499c-11e7-9fc5-ea52b89e9429');
        $headersBag->get('X-Github-Event')->willReturn('ping');
        $requestBag->get('payload')->willReturn('{}');

        $signatureChecker
            ->validate('hmac_digest', '{}')
            ->willReturn(true)
        ;

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_failed_response_when_use_case_throw_exception(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag,
        $signatureChecker,
        $useCase,
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
            ->willReturn(true)
        ;

        $useCaseRequeset = new UseCase\Request(
            'c829d500-499c-11e7-9fc5-ea52b89e9429',
            'NiR/github-dashboard',
            'ping',
            ['repository' => ['full_name' => 'NiR/github-dashboard']]
        );

        $useCase
            ->__invoke(Argument::exact($useCaseRequeset))
            ->willThrow($e = new \RuntimeException())
        ;

            $logger->error(Argument::type('string'), Argument::exact(['exception' => $e]))->shouldBeCalled();

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::failed());
    }

    function it_returns_a_successful_response_when_use_case_succeed(
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $requestBag,
        $signatureChecker,
        $useCase
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
            ->willReturn(true)
        ;

        $useCaseRequeset = new UseCase\Request(
            'c829d500-499c-11e7-9fc5-ea52b89e9429',
            'NiR/github-dashboard',
            'ping',
            ['repository' => ['full_name' => 'NiR/github-dashboard']]
        );

        $useCase
            ->__invoke(Argument::exact($useCaseRequeset))
            ->shouldBeCalled()
        ;

        $this->__invoke($request)->shouldBeLike(IngestEventResponse::succeed());
    }
}
