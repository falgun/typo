<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\SQLableInterface;

final class IsNotNull extends AbstractCompareCondition implements ConditionInterface
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' IS NOT NULL';
    }
}
