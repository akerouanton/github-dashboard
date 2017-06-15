<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Ingestion\DataAccess;

use Doctrine\DBAL\Schema\Table;
use NiR\GhDashboard\TableSchema;

class DoctrineRawEventTable implements TableSchema
{
    public function setTableSchema(Table $table)
    {
        $table->addColumn('id', 'string');
        $table->addColumn('repo', 'string');
        $table->addColumn('type', 'string');
        $table->addColumn('payload', 'string');
        $table->addColumn('date', 'datetime_immutable');

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['id']);

        return $table;
    }

    public function getTableName(): string
    {
        return 'raw_event';
    }
}
