<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent;

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

    public static function succeed(): self
    {
        return new self();
    }

    public function isSuccessful(): bool
    {
        return $this->hasErrors();
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
