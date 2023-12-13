<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class MirrorValley extends BaseMap
{
    public int $middleRow;
    public int $middleColumn;

    public function findReflections(int $smudges = 0): self
    {
        $this->middleRow = $this->findReflection($this->map, $smudges);
        $this->middleColumn = $this->findReflection($this->map->transpose(), $smudges);

        return $this;
    }

    public function count(): int
    {
        return ($this->middleRow * 100) + $this->middleColumn;
    }

    private function findReflection(Collection $rows, int $smudgesExpected = 0): int
    {
        $rows = $rows->map(fn (Collection $row) => $row->join(''));

        for ($y = 1; $y < $rows->count(); $y++) {
            $top = $rows->take($y)->reverse()->values();
            $bottom = $rows->skip($y)->values();

            $top = $top->take($bottom->count());
            $bottom = $bottom->take($top->count());

            $smudges = 0;
            for ($i = 0; $i < $top->count(); $i++) {
                $smudges += count(array_diff_assoc(
                    str_split($bottom[$i]),
                    str_split($top[$i]))
                );
            }

            if ($smudges === $smudgesExpected) {
                return $y;
            }
        }

        return 0;
    }
}
