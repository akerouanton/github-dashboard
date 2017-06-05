<?php

declare(strict_types=1);

namespace NiR\GhDashboard;

use Doctrine\DBAL\Connection;

class SchemaManager
{
    /** @var Connection */
    private $connection;

    /** @var TableSchema[] */
    private $tables;

    public function __construct(Connection $connection, array $tables)
    {
        $this->connection = $connection;
        $this->tables     = $tables;
    }

    public function createSchema()
    {
        $manager = $this->connection->getSchemaManager();
        $schema  = $manager->createSchema();

        foreach ($this->tables as $tableSchema) {
            $table = $schema->createTable($tableSchema->getTableName());
            $tableSchema->setTableSchema($table);
        }

        $sql = $schema->toSql($manager->getDatabasePlatform());
        array_map([$this->connection, 'exec'], $sql);
    }

    public function dropAndCreateDatabase()
    {
        $this->connection->getSchemaManager()->dropAndCreateDatabase($this->connection->getDriver()->getName());
    }
}
