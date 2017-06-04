<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\Infrastructure;

use NiR\GhDashboard\Ingestion\Domain;
use Doctrine\DBAL\Connection;

class DoctrineRawEventPersister implements Domain\RawEventPersister
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function persist(Domain\RawEvent $event)
    {
        $this->connection->insert('raw_event', [
            'id'      => $event->getId(),
            'repo'    => $event->getRepo(),
            'type'    => $event->getType(),
            'payload' => json_encode($event->getPayload()),
        ]);
    }
}
