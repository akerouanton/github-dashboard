<?php

namespace NiR\GhDashboard\Ingestion\Domain;

interface RawEventPersister
{
    public function persist(RawEvent $event);
}
