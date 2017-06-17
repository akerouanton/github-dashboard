<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent as UseCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function __invoke(Request $request): Response
    {
        if (!$this->validator->validate($request)) {
            return Response::failed();
        }

        $deliveryId = $request->headers->get('X-Github-Delivery');
        $eventType  = $request->headers->get('X-Github-Event');
        $payload    = $request->request->get('payload');

        try {
            $decoded = $this->jsonDecode($payload);
        } catch (\InvalidArgumentException $e) {
            return Response::failed();
        }

        if (!isset($decoded['repository'], $decoded['repository']['full_name'])) {
            return Response::failed();
        }

        $response = $this->useCase->__invoke(new UseCase\Request(
            $deliveryId,
            $decoded['repository']['full_name'],
            $eventType,
            $decoded
        ));

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
