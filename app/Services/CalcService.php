<?php

namespace App\Services;

use App\Exceptions\CalcException;
use App\Exceptions\DivisionByZeroException;
use App\Exceptions\NanResultException;
use App\Exceptions\OperationNotAllowedException;
use App\Helpers\CalcHelper;
use App\Validators\CalcValidator;

class CalcService
{
    /** @var CalcValidator  */
    private $calcValidator;

    public function __construct(CalcValidator $calcValidator)
    {
        $this->calcValidator = $calcValidator;
    }

    public function calculate(float $firstArg, float $secondArg, string $operator): float
    {
        switch ($operator) {
            case CalcHelper::MATH_ADDITION:
                $result = $firstArg + $secondArg;
                break;
            case CalcHelper::MATH_SUBTRACTION:
                $result = $firstArg - $secondArg;
                break;
            case CalcHelper::MATH_MULTIPLICATION:
                $result = $firstArg * $secondArg;
                break;
            case CalcHelper::MATH_DIVISION:
                $result = $this->doDivision($firstArg, $secondArg);
                break;
            default:
                throw new OperationNotAllowedException();
        }

        try {
            $this->calcValidator->validateResult($result);
        } catch (NanResultException $e) {
            throw new CalcException('Expected result will never been processed. Please change your formula.');
        }

        return $result;
    }

    private function doDivision(float $firstArg, float $secondArg): float
    {
        try {
            $this->calcValidator->validateDivision($secondArg);

            return $firstArg / $secondArg;
        } catch (DivisionByZeroException $e) {
            throw new CalcException('Even Jedi cannot divide by zero.');
        }
    }
}
