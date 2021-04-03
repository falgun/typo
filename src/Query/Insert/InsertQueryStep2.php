<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Insert;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;

final class InsertQueryStep2
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

    public static function fromStep1(
        Kuery $kuery,
        Table $table,
        array $columns,
        array $values,
    ): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->columns = $columns;
        $object->valueSet[] = $values;

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
        return $this->getFinalStep()
                ->execute();
    }

    public function getSQL(): string
    {
        return $this->getFinalStep()
                ->getSQL();
    }

    public function getBindValues(): array
    {
        return $this->getFinalStep()
                ->getBindValues();
    }

    private function getFinalStep(): InsertQueryFinalStep
    {
        return InsertQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->table,
                $this->columns,
                $this->valueSet
        );
    }
}
