<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http;

use NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent as UseCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class IngestEventAction
{
    private $useCase;

    private $logger;

    private $signatureChecker;

    public function __construct(UseCase\UseCase $useCase, LoggerInterface $logger, SignatureChecker $signatureChecker)
    {
        $this->useCase          = $useCase;
        $this->logger           = $logger;
        $this->signatureChecker = $signatureChecker;
    }

    public function __invoke(Request $request): IngestEventResponse
    {
        if (!$this->validateRequest($request)) {
            return IngestEventResponse::failed();
        }

        $signature  = $request->headers->get('X-Hub-Signature');
        $deliveryId = $request->headers->get('X-Github-Delivery');
        $eventType  = $request->headers->get('X-Github-Event');
        $payload    = $request->request->get('payload');

        if (!$this->signatureChecker->validate($signature, $payload)) {
            $this->logger->info(
                'Request signature does not match signed payload.',
                ['signature' => $signature, 'payload' => $payload]
            );

            return IngestEventResponse::failed();
        }

        // Decode payload or return a failed response
        try {
            $decoded = json_decode($payload, true);
        } catch (\Throwable $e) {
            return IngestEventResponse::failed();
        }

        if (!isset($decoded['repository'], $decoded['repository']['full_name'])) {
            return IngestEventResponse::failed();
        }

        // Execute use case or return failed response (and log exception)
        try {
            $response = $this->useCase->__invoke(new UseCase\Request(
                $deliveryId,
                $decoded['repository']['full_name'],
                $eventType,
                $decoded
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Exception thrown during IngestEventAction.', ['exception' => $e]);

            return IngestEventResponse::failed();
        }

        return $response->isSuccessful() ? IngestEventResponse::succeed() : IngestEventResponse::failed();
    }

    private function validateRequest(Request $request): bool
    {
        return $request->headers->has('X-Hub-Signature')
            && $request->headers->has('X-Github-Delivery')
            && $request->headers->has('X-Github-Event')
            && $request->request->has('payload')
        ;
    }
}
