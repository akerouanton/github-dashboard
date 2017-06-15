<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\DataAccess;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
        try {
            $this->connection->insert('raw_event', [
                'id' => $event->getId(),
                'repo' => $event->getRepo(),
                'type' => $event->getType(),
                'payload' => json_encode($event->getPayload()),
                'date' => $event->getDate(),
            ], [
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                'datetime_immutable',
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new Domain\RawEventAlreadyExists($event->getId(), $e);
        }
    }
}
