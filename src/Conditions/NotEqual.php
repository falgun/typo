<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;

final class NotEqual extends AbstractCompareCondition implements ConditionInterface
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        // we are using <> here, as it is sql standard, however mysql supports != too
        return $sideA->getSQL() . ' <> ' . $placeholderSQL;
    }
}
