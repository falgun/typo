<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class GreaterThanEqualTest extends AbstractIntegrationTest
{

    public function testGreaterThanEqualCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->gte(75))
            ->orWhere($userMeta->id()->gte(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->orderBy($postMeta->id())
                ->limit(1)
                ->as('')
        ));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id >= ? OR users.id >= (SELECT posts.id
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
