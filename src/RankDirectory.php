<?php

namespace Sds;

/**
  The rank directory allows you to build an index to quickly compute the
  rank() and select() functions. The index can itself be encoded as a binary
  string.
 */
class RankDirectory
{
    /**
    Used to build a rank directory from the given input string.

    @param data A javascript string containing the data, as readable using the
    BitString object.

    @param numBits The number of bits to index.

    @param t1Size The number of bits that each entry in the Level 1 table
    summarizes. This should be a multiple of t2Size.

    @param t2Size The number of bits that each entry in the Level 2 table
    summarizes.
     */
    public static function create($data, $numBits, $t1Size = 32 * 32, $t2Size = 32)
    {
        $bits = new BitString($data);
        $p = 0;
        $i = 0;
        $count1 = 0;
        $count2 = 0;
        $t1bits = ceil(log($numBits) / log(2));
        $t2bits = ceil(log($t1Size) / log(2));
        $directory = new BitWriter();
        while ($p + $t2Size <= $numBits) {
            $count2 += $bits->count($p, $t2Size);
            $i += $t2Size;
            $p += $t2Size;
            if ($i === $t1Size) {
                $count1 += $count2;
                $directory->write($count1, $t1bits);
                $count2 = 0;
                $i = 0;
            } else {
                $directory->write($count2, $t2bits);
            }
        }

        return new RankDirectory($directory->getData(), $data, $numBits, $t1Size, $t2Size);
    }

    public function __construct($directoryData, $bitData, $numBits, $t1Size, $t2Size)
    {
        $this->directory = new BitString($directoryData);
        $this->data = new BitString($bitData);
        $this->t1Size = $t1Size;
        $this->t2Size = $t2Size;
        $this->t1Bits = ceil(log($numBits) / log(2));
        $this->t2Bits = ceil(log($t1Size) / log(2));
        $this->sectionBits = ($t1Size / $t2Size - 1) * $this->t2Bits + $this->t1Bits;
        $this->numBits = $numBits;
    }

    /**
        Returns the string representation of the directory.
     */
    public function getData()
    {
        return $this->directory->getData();
    }

    /**
      Returns the number of 1 or 0 bits (depending on the "which" parameter) to
      to and including position x.
     */
    public function rank($which, $x)
    {
        if (0 === $which) {
            return $x - $this->rank(1, $x) + 1;
        }
        $rank = 0;
        $o = $x;
        $sectionPos = 0;
        if ($o >= $this->t1Size) {
            $sectionPos = ($o / $this->t1Size | 0) * $this->sectionBits;
            $rank = $this->directory->get($sectionPos - $this->t1Bits, $this->t1Bits);
            $o = $o % $this->t1Size;
        }
        if ($o >= $this->t2Size) {
            $sectionPos += ($o / $this->t2Size | 0) * $this->t2Bits;
            $rank += $this->directory->get($sectionPos - $this->t2Bits, $this->t2Bits);
        }

        $rank += $this->data->count($x - $x % $this->t2Size, $x % $this->t2Size + 1);

        return $rank;
    }

    /**
      Returns the position of the y'th 0 or 1 bit, depending on the $which
      parameter.
     */
    public function select($which, $y)
    {
        $high = $this->numBits;
        $low = -1;
        $val = -1;

        while ($high - $low > 1) {
            $probe = ($high + $low) / 2 | 0;
            $r = $this->rank($which, $probe);

            if ($r === $y) {
                // We have to continue searching after we have found it,
                // because we want the _first_ occurrence.
                $val = $probe;
                $high = $probe;
            } elseif ($r < $y) {
                $low = $probe;
            } else {
                $high = $probe;
            }
        }

        return $val;
    }
}
