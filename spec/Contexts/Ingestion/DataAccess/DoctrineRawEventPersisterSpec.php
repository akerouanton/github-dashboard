<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\DataAccess;

use Doctrine\DBAL\Connection;
use NiR\GhDashboard\Contexts\Ingestion\Domain;
use NiR\GhDashboard\Contexts\Ingestion\DataAccess\DoctrineRawEventPersister;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DoctrineRawEventPersisterSpec extends ObjectBehavior
{
    function let(Connection $connection)
    {
        $this->beConstructedWith($connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DoctrineRawEventPersister::class);
    }

    function it_is_a_raw_event_persister()
    {
        $this->shouldImplement(Domain\RawEventPersister::class);
    }

    function it_persists_raw_event(Domain\RawEvent $event, $connection)
    {
        $event->getId()->willReturn('01234');
        $event->getRepo()->willReturn('NiR/GhDashboard');
        $event->getType()->willReturn('issue');
        $event->getPayload()->willReturn(['foo' => 'bar']);
        $event->getDate()->willReturn($date = new \DateTime());

        $connection->insert('raw_event', [
            'id'      => '01234',
            'repo'    => 'NiR/GhDashboard',
            'type'    => 'issue',
            'payload' => '{"foo":"bar"}',
            'date'    => $date,
        ], Argument::type('array'))->shouldBeCalled();

        $this->persist($event);
    }
}
