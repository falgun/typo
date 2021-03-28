<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class NotInTest extends AbstractIntegrationTest
{

    public function testNotInCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->notIn(75))
            ->orWhere($userMeta->id()->notIn([25, 50, 100]))
            ->orWhere($userMeta->id()->notIn(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->as('')
        ));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id NOT IN (?) OR users.id NOT IN (?, ?, ?) OR users.id NOT IN ((SELECT posts.id
            FROM posts))
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [75, 25, 50, 100],
            $query->getBindValues()
        );;
    }
}
