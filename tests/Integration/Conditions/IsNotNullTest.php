<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class IsNotNullTest extends AbstractIntegrationTest
{

    public function testIsNotNullCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->name()->isNotNull())
            ->orWhere($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.name IS NOT NULL OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [50],
            $query->getBindValues()
        );
    }
}
