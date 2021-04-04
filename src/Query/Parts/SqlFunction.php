<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

final class SqlFunction implements ColumnLikeInterface
{

    private string $functionName;
    private ColumnLikeInterface $mainColumn;
    private ?string $columnAlias;

    private function __construct(string $functionName, ColumnLikeInterface $mainColumn)
    {
        $this->functionName = $functionName;
        $this->mainColumn = $mainColumn;
        $this->columnAlias = null;
    }

    public static function call(
        string $functionName,
        ColumnLikeInterface $mainColumn
    ): SqlFunction
    {
        return new static($functionName, $mainColumn);
    }

    public function as(string $alias): ColumnLikeInterface
    {
        $object = clone $this;
        $object->columnAlias = $alias;

        return $object;
    }

    public function getBindValues(): array
    {
        return $this->mainColumn->getBindValues();
    }

    public function getSQL(): string
    {
        $sql = $this->functionName . '(' . $this->mainColumn->getSQL() . ')';

        if (isset($this->columnAlias)) {
            $sql .= ' as ' . $this->columnAlias;
        }

        return $sql;
    }
}
