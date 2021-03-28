<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class NotEqualTest extends AbstractIntegrationTest
{

    public function testNotEqualCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->neq(1))
            ->orWhere($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id <> ? OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 50],
            $query->getBindValues()
        );
    }
}
