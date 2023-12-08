<?php

namespace App\Entities;

use App\Enums\HandScore;
use Illuminate\Support\Collection;

class CamelHand
{
    public Collection $cards;

    public int $score;

    public string $sort;

    public bool $useJokers = false;

    public function __construct(public int $bid, array $cards)
    {
        $this->cards = collect($cards);
    }

    public function parseCards(): self
    {
        $this->cards->transform(fn (string $card) => match ($card) {
            'A' => 14,
            'K' => 13,
            'Q' => 12,
            'J' => $this->useJokers ? -1 : 11,
            'T' => 10,
            default => (int) $card,
        });

        $this->score = $this->score();

        $this->sort = collect($this->score)
            ->merge($this->cards)
            ->map(fn (int $card) => str_pad(abs($card), 2, '0', STR_PAD_LEFT))
            ->flatten()
            ->join('-');

        return $this;
    }

    public function score(): int
    {
        return collect()
            ->pad(15, 0)
            ->keys()
            ->map(function (int $jokerValue) {
                return $this->cards->map(fn (int $card) => match ($card) {
                    -1 => $jokerValue,
                    default => $card,
                });
            })
            ->map(function (Collection $cards) {
                return match ($cards->unique()->count()) {
                    4 => HandScore::ONE_PAIR,
                    3 => $cards->countBy()->contains(3)
                        ? HandScore::THREE_OF_A_KIND
                        : HandScore::TWO_PAIR,
                    2 => $cards->countBy()->contains(3)
                        ? HandScore::FULL_HOUSE
                        : ($cards->countBy()->contains(4)
                            ? HandScore::FOUR_OF_A_KIND
                            : HandScore::FIVE_OF_A_KIND),
                    1 => HandScore::FIVE_OF_A_KIND,
                    default => HandScore::HIGH_CARD,
                };
            })
            ->sortByDesc('value')
            ->first()
            ->value;
    }

    public function useJokers(): self
    {
        $this->useJokers = true;

        return $this;
    }
}
