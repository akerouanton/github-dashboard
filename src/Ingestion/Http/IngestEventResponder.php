<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\Http;

use Symfony\Component\HttpFoundation\Response;

class IngestEventResponder
{
    public function __invoke(IngestEventResponse $response): Response
    {
        return $response->hasSucceed() ? new Response() : new Response('', 400);
    }
}
