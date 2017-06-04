<?php

namespace NiR\GhDashboard\Ingestion\UseCases\IngestEvent;

use NiR\GhDashboard\Ingestion\Domain\RawEvent;
use NiR\GhDashboard\Ingestion\Domain\RawEventPersister;
use NiR\GhDashboard\Ingestion\Domain\UuidGenerator;

class UseCase
{
    /** @var RawEventPersister */
    private $persister;

    /** @var UuidGenerator */
    private $idGenerator;

    public function __construct(RawEventPersister $persister, UuidGenerator $idGenerator)
    {
        $this->persister   = $persister;
        $this->idGenerator = $idGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $errors = $this->validate($request);

        if (!empty($errors)) {
            return Response::failed($errors);
        }

        $id = $this->idGenerator->generate();

        $this->persister->persist(
            new RawEvent($id, $request->getRepo(), $request->getType(), $request->getPayload())
        );

        return Response::succeeded();
    }

    private function validate(Request $request): array
    {
        $errors = [];

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
