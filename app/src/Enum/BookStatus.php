<?php

namespace App\Enum;

enum BookStatus: int
{
    case UNKNOWN = 0;
    case PUBLISH = 1;
    case MEAP = 2;

    public static function FromString(string $str): BookStatus
    {
        return match ($str) {
            'PUBLISH' => BookStatus::PUBLISH,
            'MEAP'    => BookStatus::MEAP,
            'UNKNOWN' => BookStatus::UNKNOWN,
        };
    }
}