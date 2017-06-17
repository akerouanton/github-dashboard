<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

class Response
{
    private $succeed;

    private function __construct(bool $succeed)
    {
        $this->succeed = $succeed;
    }

    public static function failed(): self
    {
        return new Response(false);
    }

    public static function succeed(): self
    {
        return new Response(true);
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
