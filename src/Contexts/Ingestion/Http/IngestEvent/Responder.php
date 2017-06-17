<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Responder
{
    public function __invoke(Response $response): HttpResponse
    {
        return $response->hasSucceed() ? new HttpResponse() : new HttpResponse('', 400);
    }
}
