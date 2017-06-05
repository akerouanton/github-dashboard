<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Tests\Ingestion\Infratructure;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use NiR\GhDashboard\Ingestion\DataAccess\DoctrineRawEventTable;
use NiR\GhDashboard\Ingestion\Domain;
use NiR\GhDashboard\Ingestion\DataAccess\DoctrineRawEventPersister;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for DoctrineRawEventPersister.
 *
 * It uses Sqlite driver to test SUT behavior, thus DB_ROOT_DIR env var is required
 * to know where to store the database. If it's not provided, tests are skipped.
 * The database name itself is auto-generated to avoid constraint violation on primary key.
 *
 * @author Albin Keroautnon <albin.kerouanton@knplabs.com>
 */
class DoctrineRawEventPersisterTest extends TestCase
{
    /** @var string */
    private $path;

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var DoctrineRawEventPersister */
    private $persister;

    public function setUp()
    {
        $rootDir = getenv('DB_ROOT_DIR');

        if (empty($rootDir)) {
            $this->markTestSkipped('Missing DB_ROOT_DIR env var.');
        }

        $this->path       = sprintf('%s/%s.sqlite', $rootDir, uniqid());
        $this->connection = DriverManager::getConnection(
            ['driver' => 'pdo_sqlite', 'path' => $this->path],
                new Configuration()
        );

        $manager = $this->connection->getSchemaManager();
        $schema  = $manager->createSchema();

        $table = new DoctrineRawEventTable();
        $table->setTableSchema($schema->createTable('raw_event'));

        // Converts $schema into sql queries and executes them
        array_map([$this->connection, 'exec'], $schema->toSql($manager->getDatabasePlatform()));

        $this->persister = new DoctrineRawEventPersister($this->connection);
    }

    public function testPersist()
    {
        $id    = 'e32b5f0f-489b-4670-b104-244eb8448ef6';
        $event = new Domain\RawEvent($id, 'NiR/GhDashboard', 'issue', ['foo' => 'bar']);

        $this->persister->persist($event);

        $count = $this->connection
            ->query(sprintf('SELECT COUNT(id) FROM raw_event WHERE id = "%s"', $id))
            ->fetchColumn(0)
        ;

        $this->assertEquals(1, $count);
    }

    public function testPersistNonUniqueId()
    {
        $id = 'e32b5f0f-489b-4670-b104-244eb8448ef6';
        $this->connection->insert('raw_event', ['id' => $id, 'repo' => '', 'type' => '', 'payload' => '']);

        $this->expectException(Domain\RawEventAlreadyExists::class);

        $event = new Domain\RawEvent($id, 'NiR/GhDashboard', 'issue', ['foo' => 'bar']);
        $this->persister->persist($event);
    }

    public function tearDown()
    {
        unset($this->persister);
        unlink($this->path);
    }
}
