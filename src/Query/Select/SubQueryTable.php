<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Typo\Query\SubQueryInterface;
use Falgun\Typo\Query\Parts\TableLikeInterface;

final class SubQueryTable implements TableLikeInterface, SubQueryInterface
{

    private SelectQueryInterface $selectQuery;
    private string $alias;

    private function __construct(SelectQueryInterface $selectQuery, string $alias = '')
    {
        $this->selectQuery = $selectQuery;
        $this->alias = $alias;
    }

    public static function fromQuery(SelectQueryInterface $selectQuery): SubQueryTable
    {
        return new static($selectQuery);
    }

    public function as(string $alias): TableLikeInterface
    {
        $subQuery = clone $this;

        $subQuery->alias = $alias;

        return $subQuery;
    }

    public function getSQL(): string
    {
        $sql = '(' . $this->selectQuery->getSQL() . ')';

        if ($this->alias) {
            $sql .= ' as ' . $this->alias;
        }

        return $sql;
    }

    public function getBindValues(): array
    {
        return $this->selectQuery->getBindValues();
    }
}
