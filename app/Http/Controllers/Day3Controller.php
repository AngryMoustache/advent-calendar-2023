<?php

namespace App\Http\Controllers;

use App\Entities\BaseMap;
use App\Entities\Schematic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day3Controller extends Controller
{
    public Schematic $schematic;

    public array $numbers = [[]];

    public array $gears = [];

    public function one()
    {
        $this->data();

        return collect($this->numbers)
            ->reject(fn (array $numbers) => collect($numbers)->pluck(1)->filter()->isEmpty())
            ->reject(fn (array $numbers) => $numbers === [[]])
            ->map(fn (array $numbers) => (int) collect($numbers)->pluck(0)->join(''))
            ->sum();
    }

    public function two()
    {
        $this->data()->map(function (string $value, int $x, int $y, BaseMap $map) {
            if ($value !== '*') {
                return;
            }

            $this->gears[] = ['x' => $x,  'y' => $y];
        });

        return collect($this->gears)
            ->map(function (array $gear) {
                return collect([
                    [$gear['x'] + 1, $gear['y']],
                    [$gear['x'] - 1, $gear['y']],
                    [$gear['x'], $gear['y'] + 1],
                    [$gear['x'], $gear['y'] - 1],
                    [$gear['x'] + 1, $gear['y'] + 1],
                    [$gear['x'] - 1, $gear['y'] + 1],
                    [$gear['x'] + 1, $gear['y'] - 1],
                    [$gear['x'] - 1, $gear['y'] - 1],
                ])
                ->map(function (array $coords) {
                    return collect($this->numbers)
                        ->filter(function (array $number) use ($coords) {
                            return collect($number)->filter(fn (array $n) =>
                                $n[2] === $coords[0] && $n[3] === $coords[1]
                            )->isNotEmpty();
                        })
                        ->map(fn (array $n) => (int) collect($n)->pluck(0)->join(''));
                })
                ->reject->isEmpty()
                ->flatten()
                ->unique();
            })
            ->filter(fn (Collection $points) => $points->count() === 2)
            ->map(fn (Collection $n) => $n->values())
            ->map(fn (Collection $n) => $n[0] * $n[1])
            ->sum();
    }

    private function data()
    {
        $file = File::get(public_path('inputs/3-1.txt'), 'r');

        $data = new Schematic(
            collect(explode(PHP_EOL, $file))
                ->filter()
                ->map(fn (string $line) => collect(str_split($line)))
        );

        $this->schematic = $data;

        $data->map(function (string $value, int $x, int $y, BaseMap $map) {
            if (! is_numeric($value)) {
                $this->numbers[count($this->numbers)][] = [];

                return;
            }

            $this->numbers[count($this->numbers) - 1][] = [
                $value,
                $map->surroundings($x, $y)
                    ->reject(fn (?string $i) => is_numeric($i) || $i === '.')
                    ->isNotEmpty(),
                $x,
                $y,
            ];
        });

        $this->numbers = collect($this->numbers)
            ->reject(fn (array $number) => $number === [[]])
            ->map(fn (array $number) => collect($number)->filter()->values()->toArray())
            ->values()
            ->toArray();

        return $data;
    }
}
