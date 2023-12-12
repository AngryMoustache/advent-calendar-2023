<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day12Controller extends Controller
{
    public Collection $seen;

    public function one()
    {
        return $this->data()
            ->map(fn (Collection $line) => $this->calculate($line[0], $line[1]))
            ->sum();
    }

    public function two()
    {
        return $this->data()
            ->map(function (Collection $line) {
                $newLine = [clone $line[0], clone $line[1]];

                for ($i = 0; $i < 4; $i++) {
                    $newLine[0]->push(['?', ...$line[0]]);
                    $newLine[1]->push($line[1]);
                }

                return $this->calculate(
                    $newLine[0]->flatten(),
                    $newLine[1]->flatten()
                );
            })
            ->sum();
    }

    public function calculate(Collection $line, Collection $numbers): int
    {
        $line = $line->values();
        $numbers = $numbers->values();

        if ($line->isEmpty()) {
            return (int) $numbers->isEmpty();
        }

        if ($numbers->isEmpty()) {
            return (int) ! $line->contains('#');
        }

        $key = $line->implode('') . '--' . $numbers->implode('');
        if ($this->seen->has($key)) {
            return $this->seen->get($key);
        }

        $result = 0;

        if (in_array($line->first(), ['.', '?'])) {
            $result += $this->calculate($line->slice(1), $numbers);
        }

        if (in_array($line->first(), ['#', '?'])) {
            if (
                $numbers->first() <= $line->count()
                && ! $line->slice(0, $numbers->first())->contains('.')
                && ($numbers->first() === $line->count() || ($line[$numbers->first()] ?? null) !== '#')
            ) {
                $result += $this->calculate(
                    $line->slice($numbers->first() + 1),
                    $numbers->slice(1)
                );
            }
        }

        $this->seen->put($key, $result);

        return $result;
    }

    private function data(): Collection
    {
        $file = File::get(public_path("inputs/12-1.txt"), 'r');

        $this->seen = collect();

        return collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(function (string $line) {
                $line = explode(' ', $line);

                return collect([
                    collect(str_split($line[0])),
                    collect(explode(',', $line[1]))->map(fn (string $num) => (int) $num),
                ]);
            });
    }
}
