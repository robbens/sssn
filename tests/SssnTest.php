<?php

use PHPUnit\Framework\TestCase;
use Robbens\Sssn\Sssn;

class SssnTest extends TestCase
{

    public function testMale()
    {
        $male = Sssn::make()->male();

        $this->assertTrue(boolval($male->gender % 2));
    }

    public function testFemale()
    {
        $female = Sssn::make()->female();

        $this->assertNotTrue(boolval($female->gender % 2));
    }

    public function testMake()
    {
        $validSsn = '6205251231';

        $generatedSsn = Sssn::make('620525', '123');

        $this->assertEquals($validSsn, $generatedSsn->get());
    }
}
