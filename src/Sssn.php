<?php

namespace Robbens\Sssn;

use Illuminate\Support\Collection;

class Sssn
{
    public $date;
    public $key;
    public $gender;
    public $ssn;

    /**
     * @param string $date
     * @param string|null $key
     * @return $this
     */
    public static function make(string $date = null, string $key = null)
    {
        return (new static)->create($date, $key);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function female()
    {
        $this->gender = $this->generateGender('female');

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function male()
    {
        $this->gender = $this->generateGender('male');

        return $this;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->ssn;
    }

    /**
     * @param string $date
     * @param string|null $key
     * @return $this
     * @throws \Exception
     */
    protected function create(string $date = null, string $key = null)
    {
        $this->date = $date ?? self::makeDate();
        $this->key = $key ?? mt_rand(10, 99);
        $this->gender = strlen($this->key) === 2 ? $this->generateGender() : null;

        // Combine date, key and gender key.
        $ssnPartial = $this->date . $this->key . $this->gender;

        // Remove the leading two numbers if the total if above 9.
        if (strlen($ssnPartial) > 9) {
            $ssnPartial = substr($ssnPartial, 2);
        }

        /**
         * Calculate the final control number. i.e 998877-112X
         *
         * Split all numbers in to an array and multiply every other with 2.
         * Sum all numbers in the array. Numbers above 9 will be split in to two and summed individually.
         *
         * Example: 620525-123
         * 6    2   0   5   2   5 - 1   2   3
         * 12 + 2 + 0 + 5 + 4 + 5 + 2 + 2 + 6 = 38
         */

        $combined = Collection::make(str_split($ssnPartial))
            ->map(function ($number, $index) {
                return $index % 2 ? $number : $number * 2;
            })->sum(function ($number) {
                if ($number >= 10) {
                    return str_split($number)[0] + str_split($number)[1];
                }

                return $number;
            });

        /**
         * Get the last number from 38 (8) and remove it from the number 10.
         * So we do 10 - 8 = 2. That's our final control number. Yay!
         *
         * However! If the control number is 10. We return 0.
         */
        $controlNumber = (10 - substr($combined, 1, 1));

        if ($controlNumber >= 10) {
            $controlNumber = 0;
        }

        $this->ssn = substr_replace($ssnPartial . $controlNumber, '-', 5,1);

        return $this;
    }

    /**
     * Male numbers are odd. Female numbers are even. Just
     * return a random number if no gender is passed.
     *
     * @param $gender string
     * @return int
     * @throws \Exception
     */
    private function generateGender(string $gender = null): int
    {
        $number = mt_rand(0, 9);

        if ($gender === null) {
            return (int)$number;
        }

        if ($gender === 'male') {
            return (int)$number | 1;
        }

        if ($gender === 'female') {
            return (int)$number & ~1;
        }

        throw new \InvalidArgumentException('Invalid gender. Use male or female.');
    }

    /**
     * Randomize date in format YYMMDD
     *
     * @return string
     */
    private static function makeDate()
    {
        $year = self::pad(mt_rand(0, 99));
        $month = self::pad(mt_rand(1, 12));
        $day = self::pad(mt_rand(1, 31));

        return $year . $month . $day;
    }

    /**
     * Add a leading zero.
     *
     * @param $number
     * @return string
     */
    private static function pad($number)
    {
        return str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->ssn;
    }
}
