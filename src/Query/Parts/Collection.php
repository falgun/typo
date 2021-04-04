<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

final class Collection
{

    /** @var array<int, SQLableInterface> */
    private array $parts;
    private string $prefix;

    /**
     * @param array<int, SQLableInterface> $parts
     * @param string $prefix
     */
    private function __construct(array $parts, string $prefix)
    {
        $this->parts = $parts;
        $this->prefix = $prefix;
    }

    /**
     * @param array<int, SQLableInterface> $parts
     * @param string $prefix
     */
    public static function from(array $parts, string $prefix): Collection
    {
        return new static($parts, $prefix);
    }

    public function join(string $seperator = ', '): string
    {
        if ($this->parts === []) {
            return '';
        }

        return ($this->prefix ? $this->prefix . ' ' : '') .
            implode(
                $seperator,
                array_map(
                    fn(SQLableInterface $part) => $part->getSQL(),
                    $this->parts
                )
        );
    }
}
