<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Conditions;

use Falgun\Typo\Query\SQLableInterface;

final class In extends AbstractCompareCondition implements ConditionInterface
{

    /**
     * @param mixed $sideB
     *
     * @return string
     */
    protected function prepareValuePlaceholder($sideB): string
    {
        return is_array($sideB) ? implode(', ', array_fill(0, count($sideB), '?')) : '?';
    }

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' IN (' . $placeholderSQL . ')';
    }

    /**
     * @param mixed $sideB
     *
     * @return array
     */
    protected function prepareBindValues($sideB): array
    {
        return is_array($sideB) ? $sideB : [$sideB];
    }
}
