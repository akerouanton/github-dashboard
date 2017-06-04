<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\UseCases\IngestEvent;

class Response
{
    /** @var array|string[] */
    private $errors;

    private function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public static function failed(array $errors = []): self
    {
        return new self($errors);
    }

    public static function succeeded(): self
    {
        return new self();
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
