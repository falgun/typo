<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class LesserThanTest extends AbstractIntegrationTest
{

    public function testLesserThanCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->lt(75))
            ->orWhere($userMeta->id()->lt(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->orderBy($postMeta->id()->desc())
                ->limit(1)
                ->as('')
        ));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id < ? OR users.id < (SELECT posts.id
            FROM posts
            ORDER BY posts.id DESC
            LIMIT ?)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [75, 1],
            $query->getBindValues()
        );
    }
}
