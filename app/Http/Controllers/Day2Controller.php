<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class Day1Controller extends Controller
{
    public array $wordMap = [
        'zero' => 0,
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
    ];

    public function one()
    {
        return $this->data()
            ->map(fn (string $line) => preg_replace('/[^0-9]/', '', $line))
            ->map(fn (string $line) => $line[0] . ($line[strlen($line) - 1] ?? $line[0]))
            ->sum();
    }

    public function two()
    {
        return $this->data()
            ->map(function (string $line) {
                $results = collect();
                $toCheck = collect();

                for ($i = 0; $i < strlen($line); $i++) {
                    $toCheck = $toCheck
                        ->map(fn (string $word) => $word . $line[$i])
                        ->prepend($line[$i])
                        ->reject(function (string $word) use ($results) {
                            if (isset($this->wordMap[$word])) {
                                $results->push($this->wordMap[$word]);

                                return true;
                            }

                            return false;
                        });

                    if (is_numeric($line[$i])) {
                        $results->push($line[$i]);
                    }
                }

                return $results->join('');
            })
            ->map(fn (string $line) => $line[0] . ($line[strlen($line) - 1] ?? $line[0]))
            ->sum();
    }

    private function data()
    {
        $file = File::get(public_path('inputs/1-1.txt'), 'r');

        return collect(explode(PHP_EOL, $file))->filter();
    }
}
