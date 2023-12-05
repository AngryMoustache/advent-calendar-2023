<?php

namespace App\Entities;

class SeedRange
{
    public function __construct(
        public array $from,
        public array $to,
    ) {
        //
    }

    public function check(int $seed): bool
    {
        return ($seed >= $this->from[0] && $seed <= $this->from[1]);
    }

    public function value(int $seed): int
    {
        return $this->to[0] + ($seed - $this->from[0]);
    }
}
