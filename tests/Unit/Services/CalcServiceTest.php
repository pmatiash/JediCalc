<?php

use App\Exceptions\CalcException;
use App\Services\CalcService;
use App\Validators\CalcValidator;
use PHPUnit\Framework\TestCase;

class CalcServiceTest extends TestCase
{
    public function testAddition()
    {
        $calc = new CalcService(new CalcValidator());
        $result = $calc->calculate(1,1,'+');
        $this->assertEquals(2, $result);

        $result = $calc->calculate(100,-300,'+');
        $this->assertEquals(-200, $result);

        $result = $calc->calculate(1.00000000000000001,0.00000000000000001,'+');
        $this->assertEquals(1.00000000000000002, $result);

        $result = $calc->calculate(10000000000E+10000000000,10000000000E+10000000000,'+');
        $this->assertEquals(INF, $result);

        $result = $calc->calculate(0,0,'+');
        $this->assertEquals(0, $result);
    }

    public function testSubtraction()
    {
        $calc = new CalcService(new CalcValidator());
        $result = $calc->calculate(1,1,'-');
        $this->assertEquals(0, $result);

        $result = $calc->calculate(100,-300,'-');
        $this->assertEquals(400, $result);

        $result = $calc->calculate(-100,300,'-');
        $this->assertEquals(-400, $result);

        $result = $calc->calculate(1.0000000000000001,0.00000000000000001,'-');
        $this->assertEquals(1.00000000000000009, $result);

        $result = $calc->calculate(0,1,'-');
        $this->assertEquals(-1, $result);

        $result = $calc->calculate(10000000000E+10000000000,1,'-');
        $this->assertEquals(INF, $result);

        $this->expectException(CalcException::class);
        // NAN value in PHP should not be compared with any other value but NAN
        $calc->calculate(10000000000E+10000000000,10000000000E+10000000000,'-');
    }

    public function testMultiplication()
    {
        $calc = new CalcService(new CalcValidator());
        $result = $calc->calculate(1,1,'*');
        $this->assertEquals(1, $result);

        $result = $calc->calculate(100,-1,'*');
        $this->assertEquals(-100, $result);

        $result = $calc->calculate(-100,-1,'*');
        $this->assertEquals(100, $result);

        $result = $calc->calculate(0,1,'*');
        $this->assertEquals(0, $result);

        $result = $calc->calculate(10000000000E+10000000000,1,'*');
        $this->assertEquals(INF, $result);

        $calc->calculate(10000000000E+10000000000,10000000000E+10000000000,'*');
        $this->assertEquals(INF, $result);

        $this->expectException(CalcException::class);
        // NAN value in PHP should not be compared with any other value but NAN
        $calc->calculate(10000000000E+10000000000,0,'*');
    }

    public function testDivision()
    {
        $calc = new CalcService(new CalcValidator());
        $result = $calc->calculate(1,1,'/');
        $this->assertEquals(1, $result);

        $result = $calc->calculate(100,-1,'/');
        $this->assertEquals(-100, $result);

        $result = $calc->calculate(-1,-100,'/');
        $this->assertEquals(0.01, $result);

        $result = $calc->calculate(0,1,'/');
        $this->assertEquals(0, $result);

        $result = $calc->calculate(10000000000E+10000000000,1,'/');
        $this->assertEquals(INF, $result);

        $this->expectException(CalcException::class);
        // INF/INF returns NAN. NAN value in PHP should not be compared with any other value but NAN
        $calc->calculate(10000000000E+10000000000,10000000000E+10000000000,'/');
    }

    public function testDivisionByZero()
    {
        $calc = new CalcService(new CalcValidator());
        $this->expectException(CalcException::class);
        // Division by zero is forbidden
        $calc->calculate(1,0,'/');
    }
}
