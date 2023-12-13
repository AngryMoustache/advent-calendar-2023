<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class MirrorValley extends BaseMap
{
    public int $middleRow;
    public int $middleColumn;

    public function findReflections(bool $smudge = false): self
    {
        $this->middleRow = $this->findReflection($this->map, $smudge);
        $this->middleColumn = $this->findReflection($this->map->transpose(), $smudge);

        return $this;
    }

    public function count(): int
    {
        return ($this->middleRow * 100) + $this->middleColumn;
    }

    private function findReflection(Collection $rows, bool $smudge): int
    {
        $rows = $rows->map(fn (Collection $row) => $row->join(''));

        for ($y = 1; $y < $rows->count(); $y++) {
            $top = $rows->take($y)->reverse()->values();
            $bottom = $rows->skip($y)->values();

            $top = $top->take($bottom->count());
            $bottom = $bottom->take($top->count());

            if ($top->toArray() === $bottom->toArray()) {
                return $y;
            }
        }

        return 0;
    }
}
