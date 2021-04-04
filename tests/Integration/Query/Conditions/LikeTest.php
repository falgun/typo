<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class LikeTest extends AbstractIntegrationTest
{

    public function testLikeCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->name()->like('%Name%'))
            ->orWhere($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.name LIKE ? OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['%Name%', 50],
            $query->getBindValues()
        );
    }
}
