<?php

namespace App\Entities;

use App\Enums\Pipe;
use Illuminate\Support\Collection;

class PipeMap extends BaseMap
{
    public string $flowDirection = '';

    public static $N = [0, -1];
    public static $E = [1, 0];
    public static $S = [0, 1];
    public static $W = [-1, 0];

    public function flow(array $start, string $direction, null | Collection $loop = null)
    {
        $loop ??= collect();
        $loop->push([...$start, $this->flowDirection, $direction]);

        $this->flowDirection = $direction;

        [$x, $y] = $this::${$this->flowDirection};
        $newStart = [$start[0] + $x, $start[1] + $y];
        $pipe = $this->point($newStart[0], $newStart[1]);

        if ($pipe === 'S') {
            return $loop;
        }

        if ($pipe === null) {
            return false;
        }

        $flow = Pipe::make($pipe)->flow($this->flowDirection);

        if (! $flow) {
            return false;
        }

        return $this->flow($newStart, $flow, $loop);
    }
}
