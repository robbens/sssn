<?php

namespace Robbens\Sssn;

use Illuminate\Support\Collection;

class Sssn
{
    public $date = '';
    public $key = '';
    public $gender = null;
    public $ssn = '';

    /**
     * @param string $date
     * @param string|null $key
     * @return $this
     */
    public static function make(string $date = null, string $key = null)
    {
        return (new static)->init($date, $key);
    }

    protected function init($date, $key)
    {
        $this->date = $date ?? self::makeDate();
        $this->key = $this->generateKey($key);
        $this->gender = $this->generateGender();
        $this->create();

        return $this;
    }

    /**
     * Validate an SSN.
     *
     * @param string $ssn
     * @return bool
     */
    public static function validate(string $ssn)
    {
        $ssn = self::clean($ssn);
        $ssnPartial = substr_replace($ssn, '', 9);

        $checksum = self::luhn($ssnPartial);

        if ($ssnPartial . $checksum !== $ssn) {
            return false;
        }

        return true;
    }

    /**
     * Strips all characters but numbers.
     *
     * @param string $ssn
     * @return string|string[]|null
     */
    public static function clean(string $ssn)
    {
        return preg_replace('/[^0-9]/', '', $ssn);
    }

    /**
     * @param null $number
     * @return $this
     */
    public function female($number = null)
    {
        $this->gender = $number ?? $this->generateGender('female');
        $this->create();

        return $this;
    }

    /**
     * @param null $number
     * @return $this
     */
    public function male($number = null)
    {
        $this->gender = $number ?? $this->generateGender('male');
        $this->create();

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
     * @return $this
     */
    protected function create()
    {
        // Combine date, key and gender key.
        $ssnPartial = $this->date . $this->key . $this->gender;

        // Remove the leading two numbers if the length is above 9.
        if (strlen($ssnPartial) > 9) {
            $ssnPartial = substr($ssnPartial, 2);
        }

        // Calculate the last control number by using Luhn algorithm.
        $checksum = self::luhn($ssnPartial);

        // Insert a hyphen.
        $ssn = substr_replace($ssnPartial . $checksum, '-', 6, 0);

        $this->ssn = $ssn;

        return $ssn;
    }

    /**
     * Male numbers are odd. Female numbers are even. Just
     * return a random number if no gender is passed.
     *
     * @param $gender string
     * @return int
     * @throws \Exception
     */
    private function generateGender($gender = null): int
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
     * Luhn's algorithm.
     *
     * @param $ssnPartial
     * @return int|string
     *
     * @url https://en.wikipedia.org/wiki/Luhn_algorithm
     */
    protected static function luhn(string $ssnPartial)
    {
        /**
         * Calculate the final control number. i.e 998877-112X
         *
         * Split all numbers in to an array and multiply every other
         * with 2. Sum all numbers in the array and stringify.
         *
         * Example: 620525-123
         * 6    2   0   5   2   5 - 1   2   3
         * 12 + 2 + 0 + 5 + 4 + 5 + 2 + 2 + 6 = 38
         */
        $checksum = Collection::make(str_split($ssnPartial))
            ->map(function ($number, $index) {
                return $index % 2 ? $number : $number * 2;
            })->implode('');

        /**
         * Get the last number from 38 (8) and remove it from the number 10.
         * So we do 10 - 8 = 2. That's our final control number. Yay!
         *
         * However! If the control number is 10. We return 0.
         */
        $checksum = (10 - (array_sum(str_split($checksum)) % 10)) % 10;

        return $checksum;
    }

    /**
     * Randomize date in format YYMMDD
     *
     * @return string
     */
    private static function makeDate()
    {
        $timestamp = mt_rand(-102389472, 1541462400);

        return date("ymd", $timestamp);
    }

    /**
     * @param $key
     * @return string
     */
    protected function generateKey($key = null): string
    {
        if (isset($key) && strlen($key) != 2) {
            throw new \LengthException('Key has to be null or have the exact length of 2.');
        }

        if (strlen($key) == 2) {
            return $key;
        }

        return self::pad(mt_rand(1, 99));
    }

    protected static function pad($str) {
        return str_pad($str, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->create();
    }
}
