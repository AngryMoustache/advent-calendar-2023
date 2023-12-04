<?php

namespace App\Http\Controllers;

use App\Entities\ScratchCard;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day4Controller extends Controller
{
    public function one()
    {
        return $this->data()->map->points()->sum();
    }

    public function two()
    {
        $copyMap = $this->data()->mapWithKeys(fn ($card) => [$card->id => $card->copies()]);
        $cards = (clone $copyMap)->keys();
        $copies = $cards->count();

        while ($cards->isNotEmpty()) {
            $cards = $cards->map(function ($id) use (&$copies, $copyMap) {
                $newCards = collect();
                $copies += $copyMap[$id];

                for ($i = 1; $i <= $copyMap[$id]; $i++) {
                    $newCards->push($id + $i);
                }

                return $newCards;
            })->flatten();
        }

        return $copies;
    }

    private function data()
    {
        $file = File::get(public_path('inputs/4-1.txt'), 'r');

        return collect(explode(PHP_EOL, $file))->filter()->map(function (string $line) {
            return new ScratchCard(
                Str::betweenFirst($line, 'Card ', ':'),
                Str::of($line)->betweenFirst(': ', '|')->explode(' ')->filter()->map(fn ($l) => (int) $l)->values(),
                Str::of($line)->afterLast('| ')->explode(' ')->filter()->map(fn ($l) => (int) $l)->values(),
            );
        });
    }
}
