<?php

namespace App\Http\Controllers;

use App\Entities\Galaxy;
use Illuminate\Support\Facades\File;

class Day11Controller extends Controller
{
    public function one()
    {
        return $this->data(2);
    }

    public function two()
    {
        return $this->data(1000000);
    }

    private function data(int $amount): int
    {
        $file = File::get(public_path("inputs/11-1.txt"), 'r');

        return (new Galaxy(
            collect(explode(PHP_EOL, $file))
                ->filter()
                ->map(fn (string $line) => collect(str_split($line)))
        ))
            ->spot()
            ->expand($amount)
            ->count();
    }
}
