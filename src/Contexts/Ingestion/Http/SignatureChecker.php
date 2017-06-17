<?php

namespace NiR\GhDashboard\Contexts\Ingestion\Http;

class SignatureChecker
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function validate(string $signature, string $payload): bool
    {
        $hmac = hash_hmac('sha1', $payload, $this->key);

        return hash_equals($signature, $hmac);
    }
}
