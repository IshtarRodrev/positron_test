<?php

namespace App\Enum;

enum FeedbackStatus: int
{
    case UNKNOWN = 0;
    case UNPROCESSED = 1;
    case PROCESSED = 2;

    public static function FromString(string $str): FeedbackStatus
    {
        return match ($str) {
            'Unknown'    => FeedbackStatus::UNKNOWN,
            'Open'=> FeedbackStatus::UNPROCESSED,
            'Closed'  => FeedbackStatus::PROCESSED,
        };
    }
    static public function getDefault(): FeedbackStatus
    {
        return self::UNPROCESSED;
    }
}