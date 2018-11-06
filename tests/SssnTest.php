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
        $validSsn = '620525-1231';

        $generatedSsn = Sssn::make('620525', '12')->male(3);

        $this->assertEquals($validSsn, $generatedSsn->get());
    }

    public function testChecksumEqualsTen()
    {
        $validSsn = '560708-2160';

        $generatedSsn = Sssn::make('560708', '21')->female(6);

        $this->assertEquals($validSsn, $generatedSsn->get());
    }

    public function testValidate()
    {
        $generatedSsn = Sssn::make('560708', '21')->female(6);

        $valid = Sssn::validate($generatedSsn);

        $this->assertTrue($valid);
    }

}
