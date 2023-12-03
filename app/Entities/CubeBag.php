<?php

namespace App\Entities;

class CubeBag
{
    public bool $impossible = false;

    public int $red = 12;
    public int $green = 13;
    public int $blue = 14;

    public int $redTaken = 0;
    public int $greenTaken = 0;
    public int $blueTaken = 0;

    public function take(string $color, int $amount): void
    {
        $this->{"{$color}Taken"} = max($amount, $this->{"{$color}Taken"});

        if ($this->{$color} < $amount) {
            $this->impossible = true;
        }
    }

    public function impossible(): bool
    {
        return $this->impossible;
    }

    public function power(): int
    {
        return $this->redTaken * $this->greenTaken * $this->blueTaken;
    }
}
