<?php

namespace App\Http\Controllers;

use App\Entities\CubeBag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day2Controller extends Controller
{
    public function one()
    {
        return $this->data()->reject->impossible()->keys()->sum();
    }

    public function two()
    {
        return $this->data()->map->power()->sum();
    }

    private function data()
    {
        $file = File::get(public_path('inputs/2-1.txt'), 'r');

        return collect(explode(PHP_EOL, $file))->filter()->mapWithKeys(function (string $line) {
            $game = collect(Str::of($line)->after(': ')->explode('; '))->map(fn (string $gameLine) =>
                collect(explode(', ', $gameLine))->map(fn ($l) => explode(' ', $l))
            );

            return [Str::between($line, 'Game ', ':') => $game];
        })->map(function (Collection $results) {
            $game = new CubeBag;

            $results->each(function (Collection $sets) use (&$game) {
                foreach ($sets as $set) {
                    $game->take($set[1], $set[0]);
                }
            });

            return $game;
        });
    }
}
