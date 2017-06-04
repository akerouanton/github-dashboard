<?php

namespace NiR\GhDashboard\Ingestion\Infrastructure;

use NiR\GhDashboard\Ingestion\Domain;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements Domain\UuidGenerator
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
