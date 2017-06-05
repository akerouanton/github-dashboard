<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\Http;

class IngestEventResponse
{
    private $succeed;

    private function __construct(bool $succeed)
    {
        $this->succeed = $succeed;
    }

    public static function failed(): self
    {
        return new IngestEventResponse(false);
    }

    public static function succeed(): self
    {
        return new IngestEventResponse(true);
    }

    public function hasSucceed(): bool
    {
        return $this->succeed;
    }

    public function hasFailed(): bool
    {
        return !$this->succeed;
    }
}
