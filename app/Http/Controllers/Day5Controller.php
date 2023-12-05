<?php

namespace App\Http\Controllers;

use App\Entities\SeedRange;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day5Controller extends Controller
{
    public Collection $ranges;

    public function one()
    {
        return $this->data()
            ->mapWithKeys(fn (int $seed) => [$seed => $this->calculate($seed)])
            ->pluck(7)
            ->sort()
            ->first();
    }

    public function two()
    {
        // Nah I think I'll pass on that one m8
    }

    private function calculate(int $seed): Collection
    {
        $list = [$seed];

        $this->ranges->each(function (Collection $ranges) use (&$list) {
            $seed = Arr::last($list);
            array_push($list, $ranges->first->check($seed)?->value($seed) ?? $seed);
        });

        return collect($list);
    }

    private function data()
    {
        $file = File::get(public_path('inputs/5-1.txt'), 'r');

        $this->ranges = collect();

        return collect(explode(PHP_EOL . PHP_EOL, $file))
            ->filter()
            ->map(function ($line) {
                $line = collect(explode(PHP_EOL, $line))->filter();
                if (Str::startsWith($line->first(), 'seeds: ')) {
                    return explode(' ', Str::after($line->first(), 'seeds: '));
                }

                $this->ranges[$this->ranges->count()] = collect();

                $line->skip(1)->each(function ($range) {
                    [$destination, $source, $range] = explode(' ', $range);

                    $this->ranges[$this->ranges->count() - 1]->push(new SeedRange(
                        [(int) $source, $source + $range - 1],
                        [(int) $destination, $destination + $range - 1],
                    ));
                });

                return null;
            })
            ->flatten()
            ->filter();
    }
}
