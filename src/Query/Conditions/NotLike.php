<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Conditions;

use Falgun\Typo\Query\SQLableInterface;

final class NotLike extends AbstractCompareCondition implements ConditionInterface
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' NOT LIKE ' . $placeholderSQL;
    }
}
