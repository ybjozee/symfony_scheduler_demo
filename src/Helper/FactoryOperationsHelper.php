<?php

namespace App\Helper;

use DateTimeInterface;

final class FactoryOperationsHelper {

    public static function isBreakTime(DateTimeInterface $time)
    : bool {

        $hour = intval($time->format('h'));
        $isEarlyBreak = $hour >= 4 && $hour < 6;
        $isLateBreak = $hour >= 10 && $hour < 12;

        return $isEarlyBreak || $isLateBreak;
    }

    public static function isDownTime(DateTimeInterface $date)
    : bool {

        if ($date->format('l') === 'Sunday') {
            return true;
        }

        $holidays = [
            '25/12',
            '26/12',
            '31/12',
            '01/01',
        ];

        return in_array($date->format('d/m'), $holidays);
    }
}
