<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Typo\Interfaces\SubQueryInterface;
use Falgun\Typo\Query\Select\SelectQueryStep2;
use Falgun\Typo\Interfaces\TableLikeInterface;

final class SubQueryTable implements TableLikeInterface, SubQueryInterface
{

    private SelectQueryStep2 $selectQuery;
    private string $alias;

    private function __construct(SelectQueryStep2 $selectQuery, string $alias = '')
    {
        $this->selectQuery = $selectQuery;
        $this->alias = $alias;
    }

    public static function fromQuery(SelectQueryStep2 $selectQuery): SubQueryTable
    {
        return new static($selectQuery);
    }

    public function as(string $alias): TableLikeInterface
    {
        $this->alias = $alias;

        return $this;
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
