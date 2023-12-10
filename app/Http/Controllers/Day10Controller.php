<?php

namespace App\Http\Controllers;

use App\Entities\PipeMap;
use Illuminate\Support\Facades\File;

class Day10Controller extends Controller
{
    public function one()
    {
        return $this->loopPoints($this->data())->count() / 2;
    }

    public function two()
    {
        $map = $this->data();
        $loopPoints = $this->loopPoints($map);

        $counting = false;
        return $map->map(function (string $pipe, int $x, int $y) use ($loopPoints, &$counting) {
            if ($x === 0) {
                $counting = false;
            }

            $loopPoint = $loopPoints->first(fn (array $point) => $point[0] === $x && $point[1] === $y);
            $value = ($counting && $loopPoint === null) ? (int) $counting : null;

            if ($loopPoint) {
                if (
                    // Clockwise
                    $loopPoint[2] === 'N' && in_array($loopPoint[3], ['E', 'N'])
                    || ($loopPoint[2] === 'W' && in_array($loopPoint[3], ['N']))
                    || ($loopPoint[2] === 'E' && in_array($loopPoint[3], ['S']))
                    || ($loopPoint[2] === 'S' && in_array($loopPoint[3], ['S', 'W']))
                ) {
                    $counting = ! $counting;
                }
            }

            return $value;
        })->flatten()->filter()->count();
    }

    private function data()
    {
        $file = File::get(public_path("inputs/10-1.txt"), 'r');

        return new PipeMap(
            collect(explode(PHP_EOL, $file))
                ->filter()
                ->map(fn (string $line) => collect(str_split($line)))
        );
    }

    private function loopPoints(PipeMap $map)
    {
        $start = $map->firstWhere('S');

        return collect([
            $map->flow($start, 'N'),
            $map->flow($start, 'E'),
            $map->flow($start, 'S'),
            $map->flow($start, 'W'),
        ])->filter()->first();
    }
}
