<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Interfaces\ColumnLikeInterface;

final class Asterisk implements ColumnLikeInterface
{

    private string $tableName;

    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function fromTable(string $tableName): ColumnLikeInterface
    {
        return new static($tableName);
    }

    public function as(string $alias): ColumnLikeInterface
    {
        throw new \InvalidArgumentException('Alias can not be applied on Asterisk');
    }

    public function getBindValues(): array
    {
        return [];
    }

    public function getSQL(): string
    {
        return $this->tableName . '.*';
    }
}
