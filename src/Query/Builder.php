<?php
declare(strict_types=1);

namespace Falgun\Typo\Query;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Select\SelectQueryStep1;
use Falgun\Typo\Query\Insert\InsertQueryStep1;
use Falgun\Typo\Query\Update\UpdateQueryStep1;
use Falgun\Typo\Query\Delete\DeleteQueryStep1;
use Falgun\Typo\Interfaces\ColumnLikeInterface;

final class Builder
{

    private Kuery $kuery;

    public function __construct(Kuery $kuery)
    {
        $this->kuery = $kuery;
    }

    public function select(ColumnLikeInterface $column, ColumnLikeInterface ...$columns): SelectQueryStep1
    {
        array_unshift($columns, $column);

        return SelectQueryStep1::selectColumns($this->kuery, $columns);
    }

    public function insertInto(Table $table, Column $column, Column ...$columns): InsertQueryStep1
    {
        array_unshift($columns, $column);

        return InsertQueryStep1::into($this->kuery, $table, ...$columns);
    }

    public function update(Table $table): UpdateQueryStep1
    {
        return UpdateQueryStep1::fromTable($this->kuery, $table);
    }

    public function delete(Table $table): DeleteQueryStep1
    {
        return DeleteQueryStep1::fromTable($this->kuery, $table);
    }

    public function getKuery(): Kuery
    {
        return $this->kuery;
    }
}
