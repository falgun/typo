<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BetweenTest extends AbstractIntegrationTest
{

    public function testBetweenCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->between(1, 99))
            ->orWhere($userMeta->id()->eq(500));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE (users.id BETWEEN ? AND ?) OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 99, 500],
            $query->getBindValues()
        );
    }
}
