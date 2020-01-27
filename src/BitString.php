<?php

namespace Sds;

class BitString
{
    /**
      @var array Used in the get method to zero out unnecessary bits
     */
    private static $maskTop = [
        0x3f, 0x1f, 0x0f, 0x07, 0x03, 0x01, 0x00,
    ];

    /**
      @var array 256-element lookup table for quick counting of set bits in bytes
     */
    private static $bitsInByte = [
        0, 1, 1, 2, 1, 2, 2, 3, 1, 2, 2, 3, 2, 3, 3, 4, 1, 2, 2, 3, 2, 3, 3, 4, 2,
        3, 3, 4, 3, 4, 4, 5, 1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5, 2, 3,
        3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6, 1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3,
        4, 3, 4, 4, 5, 2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6, 2, 3, 3, 4,
        3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6, 3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5,
        6, 6, 7, 1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5, 2, 3, 3, 4, 3, 4,
        4, 5, 3, 4, 4, 5, 4, 5, 5, 6, 2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5,
        6, 3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7, 2, 3, 3, 4, 3, 4, 4, 5,
        3, 4, 4, 5, 4, 5, 5, 6, 3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7, 3,
        4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7, 4, 5, 5, 6, 5, 6, 6, 7, 5, 6,
        6, 7, 6, 7, 7, 8,
    ];

    /** @var string data */
    private $bytes;

    /** @var int element width in bits */
    private $unitWidth;

    /** @var int data length in bits */
    private $length;

    /**
      Create a new BitString object. The BitString class supports reading or counting
      a number of bits from an arbitrary position in the string.

      @param string $str String of data (eg, in BASE-64)
      @param int    $unitWidth default element width in bits
     */
    public function __construct($str, $unitWidth = 6)
    {
        $this->bytes = $str;
        $this->unitWidth = $unitWidth;
        $this->length = strlen($this->bytes) * $this->unitWidth;
    }

    /**
      Returns the internal string of bytes
     */
    public function getData()
    {
        return $this->bytes;
    }

    /**
        Returns a decimal number, consisting of a certain number, n, of bits
        starting at a certain position, p.

        @param int $p Starting position
        @param int $n Number of bits from position

        @return int decimal number consisting of specified bits
     */
    public function get($p, $n)
    {
        // case 1: bits lie within the given byte
        if (($p % $this->unitWidth) + $n <= $this->unitWidth) {
            return (BitWriter::ord($this->bytes[$p / $this->unitWidth | 0])
                     & BitString::$maskTop[$p % $this->unitWidth]) >>
                   ($this->unitWidth - $p % $this->unitWidth - $n);

        // case 2: bits lie incompletely in the given byte
        } else {
            $result = (BitWriter::ord($this->bytes[$p / $this->unitWidth | 0]) &
                BitString::$maskTop[$p % $this->unitWidth]);

            $l = $this->unitWidth - $p % $this->unitWidth;
            $p += $l;
            $n -= $l;

            while ($n >= $this->unitWidth) {
                $result = ($result << $this->unitWidth) | BitWriter::ord($this->bytes[$p / $this->unitWidth | 0]);
                $p += $this->unitWidth;
                $n -= $this->unitWidth;
            }

            if ($n > 0) {
                $result = ($result << $n) | (BitWriter::ord($this->bytes[$p / $this->unitWidth | 0]) >>
                    ($this->unitWidth - $n));
            }

            return $result;
        }
    }

    /**
      Counts the number of bits set to 1 starting at position p and
      ending at position p + n

      @param int $p Starting position
      @param int $n Number of bits from position

      @return int number of bits set
     */
    public function count($p, $n)
    {
        $count = 0;

        while ($n >= 8) {
            $count += self::$bitsInByte[$this->get($p, 8)];
            $p += 8;
            $n -= 8;
        }

        return $count + self::$bitsInByte[$this->get($p, $n)];
    }

    /**
      Returns the number of bits set to 1 up to and including position x.
      This is the slow implementation used for testing.
     */
    public function rank($x)
    {
        $rank = 0;
        for ($i = 0; $i <= $x; ++$i) {
            if ($this->get($i, 1)) {
                ++$rank;
            }
        }

        return $rank;
    }
}
