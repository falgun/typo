<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\JoinInterface;
use Falgun\Typo\Conditions\ConditionInterface;

final class DeleteQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /**
     * @var array<int, JoinInterface>
     */
    private array $joins;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->joins = [];
    }

    public static function fromTable(Kuery $kuery, Table $table): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;

        return $object;
    }

    public function join(JoinInterface $join): DeleteQueryStep1
    {
        $this->joins[] = $join;

        return $this;
    }

    public function innerJoin(JoinInterface $join): DeleteQueryStep1
    {
        $this->joins[] = $join->asInner();

        return $this;
    }

    public function leftJoin(JoinInterface $join): DeleteQueryStep1
    {
        $this->joins[] = $join->asLeft();

        return $this;
    }

    public function where(ConditionInterface $condition): DeleteQueryStep2
    {
        return DeleteQueryStep2::fromCondition(
                $this->kuery,
                $this->table,
                $this->joins,
                $condition,
        );
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

    private function getFinalStep(): DeleteQueryFinalStep
    {
        return DeleteQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->table,
                $this->joins,
        );
    }
}
