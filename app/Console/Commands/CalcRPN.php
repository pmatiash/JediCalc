<?php

namespace App\Console\Commands;

use App\Exceptions\CalcException;
use App\Exceptions\ManualExitException;
use App\Helpers\CalcHelper;
use App\Services\CalcService;
use App\Validators\CalcValidator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalcRPN extends Command
{
    private const CHAR_EXIT = 'q';

    /**
     * @var CalcService
     */
    private $calcService;

    /** @var CalcValidator  */
    private $calcValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc:rpn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command-line reverse polish notation (RPN) calculator.';

    public function __construct(CalcService $calcService, CalcValidator $calcValidator)
    {
        $this->calcService = $calcService;
        $this->calcValidator = $calcValidator;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->decorateWelcomeOutput();

        try {
            $this->doCommand();
        } catch (ManualExitException $e) {
            $this->info('May the force be with you!');
        } catch (Exception $e) {
            Log::error('CalcRPN exception: '.$e->getMessage());
            $this->error("It's A Trap! The Empire Strikes Back!");
        }
    }

    private function doCommand(?string $firstArg = null, ?string $result = null): void
    {
        // use prev result as a first argument and continue calculation
        if (null === $firstArg) {
            $firstArg = $this->askArgument('firstArg', 'Please insert the first argument');
        } else {
            $this->info('The first argument is: '.$result);
            $firstArg = $result;
        }

        $secondArg = $this->askArgument('secondArg', 'Please insert the second argument');
        $operator = $this->askOperator();

        try {
            $result = $this->calcService->calculate((float)$firstArg, (float)$secondArg, $operator);
        } catch (CalcException $e) {
            $this->error($e->getMessage());
            $this->line('');
            $this->doCommand($firstArg, $firstArg);
        }

        $this->decorateResultOutput($result);

        // we want to use the result as a first argument and continue calculation
        //so recursively execute the logic again
        $this->doCommand($firstArg, $result);
    }

    private function askArgument(string $fieldName, string $question): string
    {
        while (true) {
            $response = $this->ask($question);
            // manually stop the calc
            $this->checkIfShouldStop($response);
            $isValid = $this->calcValidator->validateArgument($fieldName, $response);

            if (false === $isValid) {
                $this->error($this->calcValidator->getErrors()->first($fieldName));
                continue;
            }

            return $response;
        }
    }

    private function askOperator(): string
    {
        while (true) {
            $operator = $this->anticipate(
                'Please choose the operator [' . implode(',', CalcHelper::getOperationList()) . ']',
                CalcHelper::getOperationList()
            );

            // manually stop the calc
            $this->checkIfShouldStop($operator);

            $isValid = $this->calcValidator->validateOperator($operator);

            if (false === $isValid) {
                $this->error('The Calc does not support operation like this. Please try Klingon language.');
                continue;
            }

            return $operator;
        }
    }

    private function checkIfShouldStop(?string $data): void
    {
        if (self::CHAR_EXIT === strtolower($data)) {
            throw new ManualExitException();
        }
    }

    private function decorateWelcomeOutput(): void
    {
        $this->info('Welcome to command-line RPN calculator, young Padawan.');
        $this->info('Send "Q/q" for exit or simply press Power Off button.');
    }

    private function decorateResultOutput(float $result): void
    {
        $this->line("The result is: $result");
        $this->line('');
        $this->line("==========================================");
        $this->line('');
        $this->line('');
        $this->line('');
    }
}
