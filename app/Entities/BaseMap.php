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

    public function firstWhere(mixed $value): mixed
    {
        return $this->map(function (string $pipe, int $x, int $y) use ($value) {
            return ($pipe === $value ? [$x, $y] : null);
        })->flatten(1)->filter()->first();
    }

    public function set(int $x, int $y, mixed $value): self
    {
        if (isset($this->map[$y][$x])) {
            $this->map[$y][$x] = $value;
        }

        return $this;
    }

    public function count(mixed $value): int
    {
        return $this->map(fn (string $v) => $v === $value)
            ->flatten()
            ->filter()
            ->count();
    }

    public function render()
    {
        echo '<pre>';
        $this->map->each(function (Collection $row) {
            $row->each(function ($value) {
                echo $value;
            });

            echo '<br>';
        });

        echo '</pre>';
    }
}
