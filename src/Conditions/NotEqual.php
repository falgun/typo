<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\SQLableInterface;

final class NotEqual extends AbstractCompareCondition implements ConditionInterface
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        // we are using <> here, as it is sql standard, however mysql supports != too
        return $sideA->getSQL() . ' <> ' . $placeholderSQL;
    }
}
