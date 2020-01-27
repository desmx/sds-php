<?php

namespace Sds;

class BitWriter
{
    /**
      @var string symbols used to write the bit sequence
     */
    public static $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
    
	/**
      @var array char map table
     */
    public static $charMap = ["A" => 0, "B" => 1, "C" => 2, "D" => 3, "E" => 4, "F" => 5, "G" =>
        6, "H" => 7, "I" => 8, "J" => 9, "K" => 10, "L" => 11, "M" => 12, "N" => 13, "O" =>
        14, "P" => 15, "Q" => 16, "R" => 17, "S" => 18, "T" => 19, "U" => 20, "V" =>
        21, "W" => 22, "X" => 23, "Y" => 24, "Z" => 25, "a" => 26, "b" => 27, "c" =>
        28, "d" => 29, "e" => 30, "f" => 31, "g" => 32, "h" => 33, "i" => 34, "j" =>
        35, "k" => 36, "l" => 37, "m" => 38, "n" => 39, "o" => 40, "p" => 41, "q" =>
        42, "r" => 43, "s" => 44, "t" => 45, "u" => 46, "v" => 47, "w" => 48, "x" =>
        49, "y" => 50, "z" => 51, "0" => 52, "1" => 53, "2" => 54, "3" => 55, "4" =>
        56, "5" => 57, "6" => 58, "7" => 59, "8" => 60, "9" => 61, "-" => 62, "_" =>
        63];

    /**
      @var int Width of each encoding element in bits. Here we use 6, for base-64 encoding.
     */
    public $unitWidth = 6;

    /**
      @var array Actual bit sequence
     */
    public $bits = [];

    public function __construct($unitWidth = 6)
    {
        $this->unitWidth = $unitWidth;
    }

    /**
        Returns the character unit that represents the given value. If this were
        binary data, we would simply return id.

        @param int $id   Char ID from the chars table

        @return char     Matched code
     */
    public static function chr(int $id)
    {
        return self::$chars[$id];
    }

    /**
        Returns the char code  for the given symbol.

        @param char $ch   Char from the chars table

        @return int     Matched code
     */
    public static function ord($ch)
    {
        return self::$charMap[$ch];
    }

    /**
        Returns the char code  for the given symbol.

        @param int $data     Decimal value to be written
        @param int $numBits  Number of bits in result. The result will be left aligned with zero
     */
    public function write($data, $numBits)
    {
        if ($numBits > $this->unitWidth) {
        }
        for ($i = $numBits - 1; $i >= 0; --$i) {
            if ($data & (1 << $i)) {
                $this->bits[] = 1;
            } else {
                $this->bits[] = 0;
            }
        }
    }

    /**
        Returns encoded bits to chars from charMap
     */
    public function getData()
    {
        $chars = [];
        $b = 0;
        $i = 0;
        for ($j = 0; $j < sizeof($this->bits); ++$j) {
            $b = ($b << 1) | $this->bits[$j];
            ++$i;
            if ($i === $this->unitWidth) {
                $chars[] = $this->chr($b);
                $i = $b = 0;
            }
        }
        // Get the char for the remaining bits
        if ($i) {
            $chars[] = ($this->chr($b << ($this->unitWidth - $i)));
        }

        return implode('', $chars);
    }

    /**
        Returns the bits as a human readable binary string for debugging
     */
    public function getDebugString($group = 0)
    {
        $chars = [];
        $i = 0;

        for ($j = 0; $j < sizeof($this->bits); ++$j) {
            $chars[] = (string) $this->bits[$j];
            ++$i;
            if ($i === $group) {
                $chars[] = ' ';
                $i = 0;
            }
        }

        return implode('', $chars);
    }
}
