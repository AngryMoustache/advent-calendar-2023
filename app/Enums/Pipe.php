<?php

namespace App\Enums;

class Pipe
{
    public function __construct(public string $pipe)
    {
        //
    }

    public static function make(string $pipe): self
    {
        return new self($pipe);
    }

    public function flow(string $direction): false | string
    {
        return match ($direction) {
            'N' => match ($this->pipe) {
                '|' => 'N',
                '7' => 'W',
                'F' => 'E',
                default => false,
            },
            'E' => match ($this->pipe) {
                '-' => 'E',
                'J' => 'N',
                '7' => 'S',
                default => false,
            },
            'S' => match ($this->pipe) {
                '|' => 'S',
                'J' => 'W',
                'L' => 'E',
                default => false,
            },
            'W' => match ($this->pipe) {
                '-' => 'W',
                'F' => 'S',
                'L' => 'N',
                default => false,
            },
        };
    }
}
