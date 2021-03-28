<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Conditions\Between;
use Falgun\Typo\Interfaces\SQLableInterface;

final class NotBetween extends Between
{

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' NOT BETWEEN ' . $placeholderSQL;
    }
}
