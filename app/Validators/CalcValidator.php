<?php

namespace App\Validators;

use App\Exceptions\DivisionByZeroException;
use App\Exceptions\NanResultException;
use App\Helpers\CalcHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class CalcValidator
{
    /** @var MessageBag */
    private $errors;

    public function validateArgument(string $fieldName, ?string $value): bool
    {
        $validator = Validator::make(
            [
                $fieldName => $value
            ],
            [
                $fieldName => ['required', 'numeric'],
            ],
            [
                'firstArg.required' => 'My young Padawn...required is attribute first!',
                'firstArg.numeric'  => 'My young Padawn...numeric should be attribute first!',
                'secondArg.required' => 'My young Padawn...required is attribute second!',
                'secondArg.numeric'  => 'My young Padawn...numeric should be attribute second!',
            ]
        );

        if ($validator->fails()) {
            $this->errors = $validator->errors();

            return false;
        }

        return true;
    }

    public function validateOperator(?string $operator): bool
    {
        return in_array($operator, CalcHelper::getOperationList());
    }

    public function validateDivision(float $secondArg): void
    {
        if ((float)$secondArg === (float)0) {
            throw new DivisionByZeroException();
        }
    }

    /**
     * @param float $result
     * @throws NanResultException
     *
     * Operations with INF could return NAN.
     * NAN value in PHP should not be compared with any other value but NAN
     * Simply forbid such operations
     */
    public function validateResult(float $result): void
    {
        if (is_nan($result)) {
            throw new NanResultException();
        }
    }

    public function getErrors(): ?MessageBag
    {
        return $this->errors;
    }
}
