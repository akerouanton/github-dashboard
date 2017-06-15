<?php

namespace NiR\GhDashboard\Ingestion\UseCases\IngestEvent;

use NiR\GhDashboard\Ingestion\Domain\RawEvent;
use NiR\GhDashboard\Ingestion\Domain\RawEventPersister;

class UseCase
{
    /** @var RawEventPersister */
    private $persister;

    public function __construct(RawEventPersister $persister)
    {
        $this->persister = $persister;
    }

    public function __invoke(Request $request): Response
    {
        $errors = $this->validate($request);

        if (!empty($errors)) {
            return Response::failed($errors);
        }

        $event = RawEvent::happenNow($request->getId(), $request->getRepo(), $request->getType(), $request->getPayload());
        $this->persister->persist($event);

        return Response::succeeded();
    }

    private function validate(Request $request): array
    {
        $errors = [];

        if (empty($request->getId())) {
            $errors[] = 'Missing event id.';
        }
        if (empty($request->getRepo())) {
            $errors[] = 'Missing repo name.';
        }
        if (empty($request->getType())) {
            $errors[] = 'Missing event type.';
        }
        if (empty($request->getPayload())) {
            $errors[] = 'Empty event payload.';
        }

        return $errors;
    }
}
