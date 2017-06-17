<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent;

class Request
{
    /** @var string */
    private $id;

    /** @var string */
    private $repo;

    /** @var string */
    private $type;

    /** @var array */
    private $payload;

    public function __construct(string $id, string $repo, string $type, array $payload)
    {
        $this->id      = $id;
        $this->repo    = $repo;
        $this->type    = $type;
        $this->payload = $payload;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
