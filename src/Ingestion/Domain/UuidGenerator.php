<?php

namespace NiR\GhDashboard\Ingestion\Domain;

interface UuidGenerator
{
    public function generate(): string;
}
