<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;

final class Equal implements ConditionInterface
{

    private SQLableInterface $sideA;

    /**
     *
     * @var mixed
     */
    private $sideB;

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     */
    private function __construct(SQLableInterface $sideA, $sideB)
    {
        $this->sideA = $sideA;
        $this->sideB = $sideB;
    }

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     *
     * @return static
     */
    public static function fromSides(SQLableInterface $sideA, $sideB): static
    {
        return new static($sideA, $sideB);
    }

    public function getSQL(): string
    {
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            $placeholderSQL = $this->sideB->getSQL();
        } else {
            $placeholderSQL = '?';
        }

        return $this->sideA->getSQL() . ' = ' . $placeholderSQL;
    }

    public function getBindValues(): array
    {
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            return [];
        }

        return [$this->sideB];
    }
}
