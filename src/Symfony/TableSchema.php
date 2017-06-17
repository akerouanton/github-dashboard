<?php

namespace NiR\GhDashboard\Symfony;

use Doctrine\DBAL\Schema\Table;

interface TableSchema
{
    public function setTableSchema(Table $table);

    public function getTableName(): string;
}
