<?php

namespace App\Http\Controllers;

use App\Entities\MirrorValley;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day13Controller extends Controller
{
    public function one()
    {
        return $this->data()
            ->map->findReflections()
            ->map->count()
            ->sum();
    }

    public function two()
    {
        return $this->data()
            ->map->findReflections()
            ->map->count()
            ->sum();
    }

    private function data(): Collection
    {
        $file = File::get(public_path("inputs/13-1.txt"), 'r');

        return collect(explode(PHP_EOL . PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line)=> new MirrorValley(
                collect(explode(PHP_EOL, $line))
                    ->filter()
                    ->map(fn (string $line) => collect(str_split($line)))
            ));
    }
}
