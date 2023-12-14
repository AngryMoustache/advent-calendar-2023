<?php

namespace App\Http\Controllers;

use App\Entities\BoulderMap;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day14Controller extends Controller
{
    public Collection $cycleCache;

    public function one()
    {
        return $this->data()->rotate()->tumble()->count();
    }

    public function two()
    {
        $map = $this->data()->rotate();

        $this->cycleCache = collect();

        $cycleAt = 0;
        $counts = [];

        while (true) {
            for ($i = 0; $i < 4; $i++) {
                $map->tumble()->rotate();
            }

            $cycleAt++;
            if ($this->cycleCache->contains($map->map->toArray())) {
                break;
            }

            $this->cycleCache->put($cycleAt, $map->map->toArray());
            $counts[] = $map->count();
        }

        $start = $this->cycleCache->filter(fn ($i) => $i === $map->map->toArray())->keys()->first();

        return $counts[((1000000000 - $start) % ($cycleAt - $start) + $start) - 1];
    }

    private function data(): BoulderMap
    {
        $file = File::get(public_path("inputs/14-1.txt"), 'r');

        return new BoulderMap(
            collect(explode(PHP_EOL, $file))
                ->filter()
                ->map(fn (string $line) => str_split($line))
        );
    }
}
