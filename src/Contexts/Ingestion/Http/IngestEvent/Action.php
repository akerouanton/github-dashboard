<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent as UseCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Action
{
    /** @var RequestValidator */
    private $validator;

    /** @var UseCase\UseCase */
    private $useCase;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(RequestValidator $validator, UseCase\UseCase $useCase, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->useCase   = $useCase;
        $this->logger    = $logger;
    }

    public function __invoke(ServerRequestInterface $request): Response
    {
        $body = (string) $request->getBody();

        if (!$this->validator->validate($request, $body)) {
            return Response::failed();
        }

        try {
            $decoded = $this->jsonDecode($body);
        } catch (\InvalidArgumentException $e) {
            return Response::failed();
        }

        if (!isset($decoded['repository'], $decoded['repository']['full_name'])) {
            return Response::failed();
        }

        $repository = $decoded['repository']['full_name'];
        $deliveryId = $request->getHeader('X-GitHub-Delivery')[0];
        $eventType = $request->getHeader('X-GitHub-Event')[0];

        $response = $this->useCase->__invoke(new UseCase\Request($deliveryId, $repository, $eventType, $decoded));

        return $response->isSuccessful() ? Response::succeed() : Response::failed();
    }

    private function jsonDecode($json)
    {
        $decoded = json_decode($json, true);

        if (null === $decoded && json_last_error() !== null) {
            throw new \InvalidArgumentException(sprintf('Unable to decode json. Error: %s', json_last_error_msg()));
        }

        return $decoded;
    }
}
