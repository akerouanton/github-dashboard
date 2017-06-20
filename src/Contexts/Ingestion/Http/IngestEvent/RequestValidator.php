<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class RequestValidator
{
    /** @var SignatureChecker */
    private $signatureChecker;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(SignatureChecker $signatureChecker, LoggerInterface $logger)
    {
        $this->signatureChecker = $signatureChecker;
        $this->logger           = $logger;
    }

    public function validate(ServerRequestInterface $request, string $payload): bool
    {
        if (!$request->hasHeader('X-Hub-Signature')
            || !$request->hasHeader('X-GitHub-Delivery')
            || !$request->hasHeader('X-GitHub-Event')
            || empty($payload)
        ) {
            return false;
        }

        $signature = $request->getHeader('X-Hub-Signature')[0];
        $deliveryId = $request->getHeader('X-GitHub-Delivery')[0];
        $eventType = $request->getHeader('X-GitHub-Event')[0];

        if (strpos($signature, '=') === false || empty($deliveryId) || empty($eventType)) {
            return false;
        }

        list(,$hmac) = explode('=', $signature);

        if (!$this->signatureChecker->validate($hmac, $payload)) {
            $this->logger->info(
                'Request signature does not match signed payload.',
                ['signature' => $signature, 'payload' => $payload]
            );

            return false;
        }

        return true;
    }
}
