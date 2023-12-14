<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class BoulderMap extends BaseMap
{
    public Collection $lineCache;
    public Collection $mapCache;

    public function __construct(public Collection $map)
    {
        $this->lineCache = collect();
        $this->mapCache = collect();
    }

    public function rotate(): self
    {
        $this->map = $this->map->transpose()->map(fn (Collection $line) => $line->reverse()->values());

        return $this;
    }

    public function tumble(): self
    {
        $cacheKey = $this->map->map->join('')->join('');
        if ($this->mapCache->has($cacheKey)) {
            $this->map = $this->mapCache->get($cacheKey);

            return $this;
        }

        $this->map->transform(function (Collection $line) {
            $cacheKey = $line->join('');
            if ($this->lineCache->has($cacheKey)) {
                return $this->lineCache->get($cacheKey);
            }

            $groups = [[]];

            $line = $line->each(function (string $char) use (&$groups) {
                if ($char === '#') {
                    $groups[] = [];
                } else {
                    $groups[count($groups) - 1][] = $char;
                }
            });

            $line = collect($groups)->map(function (array $group) {
                return collect($group)->sort()->prepend('#')->values();
            })->flatten()->skip(1)->values();

            $this->lineCache->put($cacheKey, $line);

            return $line;
        });

        $this->mapCache->put($cacheKey, $this->map);

        return $this;
    }

    public function count(): int
    {
        return $this->map->sum(fn (Collection $line) => $line
            ->filter(fn (string $char) => $char === 'O')
            ->keys()
            ->reduce(fn (int $carry, int $key) => $carry + ($key + 1), 0)
        );
    }
}
