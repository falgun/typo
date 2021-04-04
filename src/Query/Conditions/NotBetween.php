<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Conditions;

use Falgun\Typo\Query\Conditions\Between;
use Falgun\Typo\Query\SQLableInterface;

final class NotBetween extends Between
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' NOT BETWEEN ' . $placeholderSQL;
    }
}
