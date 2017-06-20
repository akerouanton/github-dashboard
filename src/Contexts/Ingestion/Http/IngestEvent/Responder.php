<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use Http\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class Responder
{
    /** @var ResponseFactory */
    private $factory;

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Response $response): HttpResponseInterface
    {

        return $response->hasSucceed() ? $this->factory->createResponse(200) : $this->factory->createResponse(400);
    }
}
