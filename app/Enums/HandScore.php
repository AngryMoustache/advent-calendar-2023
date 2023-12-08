<?php

namespace App\Enums;

enum HandScore: int
{
    case HIGH_CARD = 1;
    case ONE_PAIR = 2;
    case TWO_PAIR = 3;
    case THREE_OF_A_KIND = 4;
    case FULL_HOUSE = 5;
    case FOUR_OF_A_KIND = 6;
    case FIVE_OF_A_KIND = 7;
}
