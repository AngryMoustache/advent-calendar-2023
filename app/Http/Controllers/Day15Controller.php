<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Day15Controller extends Controller
{
    public function one()
    {
        return $this->data()->reduce(fn (int $carry, string $code) => $carry + $this->hash($code), 0);
    }

    public function two()
    {
        $boxes = collect()->pad(256, null)->mapInto(Collection::class);

        $this->data()
            ->map(fn (string $code) => Str::of($code)->replace('=', '-')->explode('-')->filter())
            ->each(function (Collection $code) use (&$boxes) {
                $box = $boxes->get($this->hash($code[0]));

                ($code->count() === 1)
                    ? $box->forget($code[0])
                    : $box->put($code[0], $code);
            });

        return $boxes->reduce(function (int $carry, Collection $box, int $boxKey) {
            return $carry + $box->values()->reduce(function (int $carry, Collection $lens, int $lensKey) use ($boxKey) {
                return (($lensKey + 1) * ($boxKey + 1) * $lens[1]) + $carry;
            }, 0);
        }, 0);
    }

    private function data(): Collection
    {
        $file = File::get(public_path("inputs/15-1.txt"), 'r');

        return collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn ($line) => explode(',', $line))
            ->flatten()
            ->filter();
    }

    private function hash(string $code): int
    {
        return collect(str_split($code))->reduce(function (int $carry, string $code) {
            return (($carry + ord($code)) * 17) % 256;
        }, 0);
    }
}
