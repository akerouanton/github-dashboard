<?php

namespace NiR\GhDashboard\Contexts\Ingestion\Domain;

interface RawEventPersister
{
    /**
     * @param RawEvent $event
     *
     * @throws RawEventAlreadyExists
     */
    public function persist(RawEvent $event);
}
