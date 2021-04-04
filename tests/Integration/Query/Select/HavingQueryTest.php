<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Select;

use Falgun\Typo\Query\Parts\SqlFunction;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Query\Conditions\GreaterThan;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class HavingQueryTest extends AbstractIntegrationTest
{

    public function testSingleGroupHaving()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->name())
            ->from($userMeta->table())
            ->groupBy($userMeta->name())
            ->having(
            GreaterThan::fromSides(
                SqlFunction::call('COUNT',
                    $userMeta->name()
                ),
                1
            )
        );

        $this->assertSame(
            <<<SQL
            SELECT users.name
            FROM users
            GROUP BY users.name
            HAVING COUNT(users.name) > ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testMultipleHavingCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->name())
            ->from($userMeta->table())
            ->groupBy($userMeta->name())
            ->having(GreaterThan::fromSides(
                SqlFunction::call('COUNT',
                    $userMeta->name()
                ),
                1
            )
            ->and(GreaterThan::fromSides(
                    SqlFunction::call('COUNT',
                        $userMeta->name()
                    ),
                    2
                )
            )
        );

        $this->assertSame(
            <<<SQL
            SELECT users.name
            FROM users
            GROUP BY users.name
            HAVING (COUNT(users.name) > ? AND COUNT(users.name) > ?)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 2],
            $query->getBindValues()
        );
    }
}
