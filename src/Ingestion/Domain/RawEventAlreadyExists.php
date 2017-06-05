<?php

namespace NiR\GhDashboard\Ingestion\Domain;

class RawEventAlreadyExists extends \Exception
{
    public function __construct(string $eventId, \Exception $previous = null)
    {
        parent::__construct(sprintf('Raw event "%s" already exists.', $eventId), 0, $previous);
    }
}
