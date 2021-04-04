<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class NotBetweenTest extends AbstractIntegrationTest
{

    public function testNotBetweenCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->notBetween(1, 99))
            ->orWhere($userMeta->id()->eq(500));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE (users.id NOT BETWEEN ? AND ?) OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 99, 500],
            $query->getBindValues()
        );
    }
}
