<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class BaseMap
{
    public function __construct(public Collection $map)
    {
        //
    }

    public function map(callable $callback)
    {
        return $this->map->map(function (Collection $row, int $y) use ($callback) {
            return $row->map(function ($value, int $x) use ($callback, $y) {
                return $callback($value, $x, $y, $this);
            });
        });
    }

    public function point(int $x, int $y): mixed
    {
        return $this->map[$y][$x] ?? null;
    }

    public function surroundings(int $x, int $y): Collection
    {
        return collect([
            $this->point($x + 1, $y),
            $this->point($x, $y + 1),
            $this->point($x - 1, $y),
            $this->point($x, $y - 1),
            $this->point($x - 1, $y - 1),
            $this->point($x - 1, $y + 1),
            $this->point($x + 1, $y - 1),
            $this->point($x + 1, $y + 1),
        ])->filter();
    }

    public function adjacent(int $x, int $y): Collection
    {
        return collect([
            $this->point($x + 1, $y),
            $this->point($x, $y + 1),
            $this->point($x - 1, $y),
            $this->point($x, $y - 1),
        ])->filter();
    }
}
