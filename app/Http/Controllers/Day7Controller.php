<?php

namespace App\Http\Controllers;

use App\Entities\CamelHand;
use Illuminate\Support\Facades\File;

class Day7Controller extends Controller
{
    public function one()
    {
        return $this->data()
            ->map->parseCards()
            ->sortBy('sort')
            ->values()
            ->map(fn (CamelHand $hand, int $key) => $hand->bid * ($key + 1))
            ->sum();
    }

    public function two()
    {
        return $this->data()
            ->map->useJokers()
            ->map->parseCards()
            ->sortBy('sort')
            ->values()
            ->map(fn (CamelHand $hand, int $key) => $hand->bid * ($key + 1))
            ->sum();
    }

    private function data()
    {
        $file = File::get(public_path("inputs/7-1.txt"), 'r');

        return collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line) => explode(' ', $line))
            ->map(fn (array $line) => new CamelHand($line[1], str_split($line[0])));
    }
}
