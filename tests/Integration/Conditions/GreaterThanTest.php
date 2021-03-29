<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class GreaterThanTest extends AbstractIntegrationTest
{

    public function testGreaterThanCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->gt(75))
            ->orWhere($userMeta->id()->gt(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->orderBy($postMeta->id()->asc())
                ->limit(1)
                ->as('')
        ));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id > ? OR users.id > (SELECT posts.id
            FROM posts
            ORDER BY posts.id ASC
            LIMIT 1)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [75],
            $query->getBindValues()
        );
    }
}
