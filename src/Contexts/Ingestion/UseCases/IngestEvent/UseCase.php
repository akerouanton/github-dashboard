<?php

namespace NiR\GhDashboard\Contexts\Ingestion\UseCases\IngestEvent;

use NiR\GhDashboard\Contexts\Ingestion\Domain\RawEvent;
use NiR\GhDashboard\Contexts\Ingestion\Domain\RawEventAlreadyExists;
use NiR\GhDashboard\Contexts\Ingestion\Domain\RawEventPersister;

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

        try {
            $this->persister->persist($event);
        } catch (RawEventAlreadyExists $e) {
            return Response::failed(['Event already exists.']);
        }

        return Response::succeed();
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
