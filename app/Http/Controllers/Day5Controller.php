<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day5Controller extends Controller
{
    public Collection $ranges;

    public function one()
    {
        $seeds = $this->data()->map(fn (int $seed) => [$seed, $seed + 1]);

        return $this->calculate($seeds);
    }

    public function two()
    {
        $seeds = $this->data()->chunk(2)->map(fn (Collection $range) => [
            (int) $range->first(),
            (int) $range->first() + $range->last(),
        ]);

        return $this->calculate($seeds);
    }

    private function calculate(Collection $seeds): int
    {
        $this->ranges->each(function (Collection $ranges) use (&$seeds) {
            $new = collect();

            while ($seeds->isNotEmpty()) {
                [$from, $to] = $seeds->pop();
                $matches = false;

                foreach ($ranges as $range) {
                    $overlapFrom = max($from, $range[1]);
                    $overlapTo = min($to, $range[1] + $range[2]);

                    if ($overlapFrom < $overlapTo) {
                        $matches = true;

                        $new->push([
                            $overlapFrom - $range[1] + $range[0],
                            $overlapTo - $range[1] + $range[0]
                        ]);

                        if ($overlapFrom > $from) {
                            $seeds->push([$from, $overlapFrom]);
                        }

                        if ($to < $overlapTo) {
                            $seeds->push([$overlapTo, $to]);
                        }
                    }
                }

                if (! $matches) {
                    $new->push([$from, $to]);
                }
            }

            $seeds = $new;
        });

        return $seeds->flatten()->min();
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

                $this->ranges[$this->ranges->count()] = collect([
                    ...$line->skip(1)->map(fn ($range) => collect([...explode(' ', $range)]))
                ]);

                return null;
            })
            ->flatten()
            ->filter();
    }
}
