<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Builder;
use Falgun\Kuery\Configuration;
use PHPUnit\Framework\TestCase;
use Falgun\Kuery\Connection\MySqlConnection;

abstract class AbstractIntegrationTest extends TestCase
{

    protected function getBuilder(): Builder
    {
        $configuration = Configuration::fromArray(require dirname(__DIR__) . '/../config.php');
        $connection = new MySqlConnection($configuration);
        $kuery = new Kuery($connection);

        return new Builder($kuery);
    }
}
