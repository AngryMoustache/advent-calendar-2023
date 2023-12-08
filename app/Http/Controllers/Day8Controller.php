<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day8Controller extends Controller
{
    public array $instructions;
    public Collection $passages;

    public function one()
    {
        $this->data();

        $step = 'AAA';
        $steps = 0;

        while ($step !== 'ZZZ') {
            foreach ($this->instructions as $instruction) {
                $step = $this->passages[$step][$instruction];
                $steps++;
            }
        }

        return $steps;
    }

    public function two()
    {
        $this->data();

        return collect($this->passages)
            ->keys()
            ->filter(fn (string $node) => $node[2] === 'A')
            ->map(function (string $step) {
                $steps = 0;

                while ($step[2] !== 'Z') {
                    foreach ($this->instructions as $instruction) {
                        $step = $this->passages[$step][$instruction];
                        $steps++;
                    }
                }

                return $steps;
            })
            ->reduce(fn (int | \GMP $carry, int $steps) => gmp_lcm(
                $carry,
                $steps,
            ), 1);
    }

    private function data()
    {
        $file = File::get(public_path("inputs/8-1.txt"), 'r');

        $data = collect(explode(PHP_EOL . PHP_EOL, $file))->filter();

        $this->instructions = str_split($data[0]);

        $this->passages = collect(explode(PHP_EOL, $data[1]))->filter()->mapWithKeys(function (string $line) {
            [$l, $r] = Str::of($line)
                ->after(' = ')
                ->between('(', ')')
                ->explode(', ');

            return [Str::before($line, ' = ') => [
                'L' => $l,
                'R' => $r,
            ]];
        });
    }
}
