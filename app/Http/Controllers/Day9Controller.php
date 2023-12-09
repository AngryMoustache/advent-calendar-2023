<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day9Controller extends Controller
{
    public function one()
    {
        return $this->data()->pluck(1)->sum();
    }

    public function two()
    {
        return $this->data()->pluck(0)->sum();
    }

    public function addSequences(Collection $history)
    {
        $sequences = collect();

        $sequence = $history->skip(1)->map(function (int $number, int $key) use ($history) {
            return $number - $history[$key - 1];
        });

        $sequences->push($sequence);

        if ($sequence->reject(fn (int $n) => $n === 0)->isNotEmpty()) {
            $sequences->push(...$this->addSequences($sequence));
        }

        return $sequences;
    }

    private function data()
    {
        $file = File::get(public_path("inputs/9-1.txt"), 'r');

        return collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line) => collect(explode(' ', $line)))
            ->map(function (Collection $history) {
                $sequences = collect([$history, ...$this->addSequences($history)])->reverse();

                foreach ($sequences->skip(1) as $row => $sequence) {
                    if (! isset($sequences[$row - 1])) {
                        continue;
                    }

                    $sequences[$row - 1]->push(
                        $sequence->last() + $sequences[$row - 1]->last()
                    );

                    $sequences[$row - 1]->prepend(
                        $sequences[$row - 1]->first() - $sequence->first()
                    );
                }

                return [
                    $sequences->last()->first(),
                    $sequences->last()->last(),
                ];
            });
    }
}
