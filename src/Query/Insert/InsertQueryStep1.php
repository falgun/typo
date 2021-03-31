<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Insert;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\Collection;

final class InsertQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $columns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $valueSet;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function into(Kuery $kuery, Table $table, Column ...$columns): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->columns = $columns;

        return $object;
    }

    /**
     *
     * @param mixed $values
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function values(...$values): self
    {
        if (count($this->columns) !== count($values)) {
            throw new \InvalidArgumentException('Count of values did not match with count of columns');
        }

        $this->valueSet[] = $values;

        return $this;
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
