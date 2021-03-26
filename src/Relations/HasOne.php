<?php
declare(strict_types=1);

namespace Falgun\Typo\Relations;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Builder;

final class HasOne extends AbstractRelation implements RelationInterface
{

    private const TYPE = 'OneToOne';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Builder $query;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var mixed
     */
    private $foreignMeta;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private string $foreignKey;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private string $localKey;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private string $ownerKey;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    /** @psalm-suppress MissingParamType */
    public static function via(Kuery $kuery, $foreignMeta, string $foreignKey, string $localKey): static
    {
        $relation = new static;

        $relation->query = new Builder($kuery);
        $relation->foreignMeta = $foreignMeta;
        $relation->foreignKey = $foreignKey;
        $relation->localKey = $localKey;

        return $relation;
    }
}
