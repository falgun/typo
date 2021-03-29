<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Interfaces\SQLableInterface;

final class Collection
{

    /**
     * 
     * @var array<int, SQLableInterface>
     */
    private array $parts;
    private string $type;

    /**
     * 
     * @param array<int, SQLableInterface> $parts
     * @param string $type
     */
    private function __construct(array $parts, string $type)
    {
        $this->parts = $parts;
        $this->type = $type;
    }

    /**
     * 
     * @param array<int, SQLableInterface> $parts
     * @param string $type
     */
    public static function from(array $parts, string $type): Collection
    {
        return new static($parts, $type);
    }

    public function join(string $seperator = ', '): string
    {
        return $this->type . ' ' .
            implode(
                $seperator,
                array_map(
                    fn(SQLableInterface $part) => $part->getSQL(),
                    $this->parts
                )
        );
    }
}
