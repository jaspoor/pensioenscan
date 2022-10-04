<?php

namespace App\Helpers;

use DateTime;

class AowHelper
{
    public static function getAowDate(DateTime $birthDayDate): DateTime
    {
        $aowDate = clone $birthDayDate;

        $aowTable = [
            ['1953-08-31', '1954-09-01', 66, 4],
            ['1954-08-31', '1955-09-01', 66, 4],
            ['1955-08-31', '1956-06-01', 66, 7],
            ['1956-05-31', '1957-03-01', 66, 10],
            ['1957-02-28', '1958-01-01', 67, 0],
            ['1957-12-31', '1959-01-01', 67, 0],
            ['1958-12-31', '1960-01-01', 67, 0],
            ['1959-12-31', '1961-01-01', 67, 0],
        ];

        foreach ($aowTable as $record) {
            list($from, $to, $years, $months) = $record;
            if (self::isDateBetween($from, $to, $aowDate)) {
                $aowDate
                    ->modify("+{$years} years")
                    ->modify("+{$months} months");
                
                return $aowDate;
            }
        }

        return $aowDate->modify("+67 years");
    }

    private static function isDateBetween(string $from, string $to, DateTime $date): bool {
        
        $from= new \DateTime($from);
        $to = new \DateTime($to);

        return ($date >= $from && $date <= $to);
    }
}