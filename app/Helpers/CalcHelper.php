<?php

namespace App\Helpers;

class CalcHelper
{
    public const MATH_ADDITION = '+';
    public const MATH_SUBTRACTION = '-';
    public const MATH_MULTIPLICATION = '*';
    public const MATH_DIVISION = '/';

    public static function getOperationList()
    {
        return [
            self::MATH_ADDITION,
            self::MATH_SUBTRACTION,
            self::MATH_MULTIPLICATION,
            self::MATH_DIVISION,
        ];
    }
}
