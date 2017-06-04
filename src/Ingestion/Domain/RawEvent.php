<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\Domain;

use Webmozart\Assert\Assert;

class RawEvent
{
    /** @var string */
    private $id;

    /** @var string */
    private $repo;

    /** @var string */
    private $type;

    /** @var array */
    private $payload;

    /**
     * @param string $id
     * @param string $repo
     * @param string $type
     * @param array  $payload
     *
     * @throws \InvalidArgumentException If one of the argument is empty
     */
    public function __construct(string $id, string $repo, string $type, array $payload)
    {
        Assert::notEmpty($id);
        Assert::notEmpty($repo);
        Assert::notEmpty($type);
        Assert::notEmpty($payload);

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
