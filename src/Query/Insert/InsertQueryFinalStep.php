<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Insert;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Collection;

final class InsertQueryFinalStep
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;
    private array $columns;
    private array $valueSet;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->columns = [];
        $this->valueSet = [];
    }

    public static function fromLastStep(
        Kuery $kuery,
        Table $table,
        array $columns,
        array $valueSet,
    ): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->columns = $columns;
        $object->valueSet = $valueSet;

        return $object;
    }

    public function execute(): int
    {
        $stmt = $this->kuery->run($this->getSQL(), $this->getBindValues());

        return $stmt->insert_id;
    }

    public function getSQL(): string
    {
        $sql = 'INSERT INTO ' . $this->table->getSQL() . ' (' .
            Collection::from($this->columns, '')->join() .
            ') VALUES ';

        foreach ($this->valueSet as $values) {
            $sql .= '(' . implode(', ', array_fill(0, count($values), '?')) . '), ';
        }

        return trim($sql, ', ');
    }

    public function getBindValues(): array
    {
        $bindables = [];

        foreach ($this->valueSet as $values) {
            $bindables = array_merge($bindables, $values);
        }

        return $bindables;
    }
}
