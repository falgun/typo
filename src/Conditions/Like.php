<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\SQLableInterface;

final class Like extends AbstractCompareCondition implements ConditionInterface
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' LIKE ' . $placeholderSQL;
    }
}
