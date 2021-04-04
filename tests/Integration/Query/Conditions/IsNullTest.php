<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class IsNullTest extends AbstractIntegrationTest
{

    public function testIsNullCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->name()->isNull())
            ->orWhere($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.name IS NULL OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [50],
            $query->getBindValues()
        );
    }
}
