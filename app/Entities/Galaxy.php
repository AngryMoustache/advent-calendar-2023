<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class Galaxy extends BaseMap
{
    public Collection $stars;

    public function spot(): self
    {
        $this->stars = $this->map(fn (string $value, int $x, int $y) => $value === '#' ? [$x, $y] : '.')
            ->flatten(1)
            ->reject(fn ($value) => $value === '.')
            ->values();

        return $this;
    }

    public function expand(int $amount): self
    {
        $expandedMap = clone $this->map;

        // Expand rows
        $this->map
            ->filter(fn (Collection $row) => $row->countBy()->count() === 1)
            ->keys()
            ->reverse()
            ->each(fn (int $row) => $this->stars->transform(function ($star) use ($row, $amount) {
                if ($star[1] > $row) {
                    $star[1] = $star[1] + $amount - 1;
                }

                return $star;
            }));

        // Expand columns
        $this->map
            ->transpose()
            ->filter(fn (Collection $column) => $column->countBy()->count() === 1)
            ->keys()
            ->reverse()
            ->each(fn (int $column) => $this->stars->transform(function ($star) use ($column, $amount) {
                if ($star[0] > $column) {
                    $star[0] = $star[0] + $amount - 1;
                }

                return $star;
            }));

        $this->map = $expandedMap;

        return $this;
    }

    public function count(): int
    {
        return $this->stars->map(function (array $coords, int $index) {
            return $this->stars->skip($index + 1)->map(fn (array $newCoords) =>
                abs($coords[0] - $newCoords[0]) + abs($coords[1] - $newCoords[1])
            );
        })->flatten()->sum();
    }
}
