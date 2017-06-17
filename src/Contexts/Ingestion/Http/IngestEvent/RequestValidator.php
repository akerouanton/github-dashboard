<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Contexts\Ingestion\Http\IngestEvent;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function validate(Request $request): bool
    {
        if (!$request->headers->has('X-Hub-Signature')
            || !$request->headers->has('X-Github-Delivery')
            || !$request->headers->has('X-Github-Event')
            || !$request->request->has('payload')
        ) {
            return false;
        }

        $signature = $request->headers->get('X-Hub-Signature');
        $payload   = $request->request->get('payload');

        if (!$this->signatureChecker->validate($signature, $payload)) {
            $this->logger->info(
                'Request signature does not match signed payload.',
                ['signature' => $signature, 'payload' => $payload]
            );

            return false;
        }

        return true;
    }
}
