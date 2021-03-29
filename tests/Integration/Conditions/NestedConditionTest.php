<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class NestedConditionTest extends AbstractIntegrationTest
{

    public function testNestedConditions()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->eq(1)
                ->or($userMeta->id()->gt(50)->and($userMeta->username()->in(['Admin', 'Mod']))))
            ->orWhere($userMeta->id()->between(467, 530)
                ->and($userMeta->id()->between(980, 99)))
            ->andWhere($userMeta->id()->neq(10)->and($userMeta->name()->like('%Test%')))
            ->andWhere($userMeta->id()->isNotNull());

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE (users.id = ? OR (users.id > ? AND users.username IN (?, ?))) OR ((users.id BETWEEN ? AND ?) AND (users.id BETWEEN ? AND ?)) AND (users.id <> ? AND users.name LIKE ?) AND users.id IS NOT NULL
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 50, 'Admin', 'Mod', 467, 530, 980, 99, 10, '%Test%'],
            $query->getBindValues()
        );
    }
}
