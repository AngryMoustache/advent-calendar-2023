<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class ScratchCard
{
    public function __construct(
        public int $id,
        public Collection $winners,
        public Collection $numbers,
    ) {
        //
    }

    public function getWinners(): Collection
    {
        return $this->winners->intersect($this->numbers);
    }

    public function points(): int
    {
        return match ($count = $this->getWinners()->count()) {
            0, 1, 2 => $count,
            default => pow(2, $count - 1),
        };
    }

    public function copies(): int
    {
        return $this->getWinners()->count();
    }
}
