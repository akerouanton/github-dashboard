<?php

namespace spec\NiR\GhDashboard\Ingestion\UseCases\IngestEvent;

use NiR\GhDashboard\Ingestion\Domain;
use NiR\GhDashboard\Ingestion\UseCases\IngestEvent as UseCase;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UseCaseSpec extends ObjectBehavior
{
    function let(Domain\RawEventPersister $persister)
    {
        $this->beConstructedWith($persister);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UseCase\UseCase::class);
    }

    function it_invalidates_bad_request(UseCase\Request $request)
    {
        $request->getId()->willReturn('');
        $request->getRepo()->willReturn('');
        $request->getType()->willReturn('');
        $request->getPayload()->willReturn([]);

        $this->__invoke($request)->shouldBeLike(UseCase\Response::failed([
            'Missing event id.',
            'Missing repo name.',
            'Missing event type.',
            'Empty event payload.',
        ]));
    }

    function it_persists_raw_event(UseCase\Request $request, $persister)
    {
        $request->getId()->willReturn('01234');
        $request->getRepo()->willReturn('NiR/gh-dashboard');
        $request->getType()->willReturn('issue');
        $request->getPayload()->willReturn(['foo' => 'bar']);

        $persister->persist(
            Argument::exact(new Domain\RawEvent('01234', 'NiR/gh-dashboard', 'issue', ['foo' => 'bar']))
        )->shouldBeCalled();

        $this->__invoke($request)->shouldBeLike(UseCase\Response::succeeded());
    }
}
