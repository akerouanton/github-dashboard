<?php

namespace spec\NiR\GhDashboard\Ingestion\Infrastructure;

use NiR\GhDashboard\Ingestion\Domain;
use NiR\GhDashboard\Ingestion\Infrastructure\UuidGenerator;
use PhpSpec\ObjectBehavior;

class UuidGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UuidGenerator::class);
    }

    function it_is_a_uuid_generator()
    {
        $this->shouldImplement(Domain\UuidGenerator::class);
    }
}
