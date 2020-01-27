<?php

namespace Sds\Tests;

use PHPUnit\Framework\TestCase;
use Sds\BitString;

class BitStringTest extends TestCase
{
    public function testBitStringGetDecimalNumberForGivenPosition(): void
    {
        $str = new BitString('v__AAB_BDFHJLNPRTVXZbde');
        $res = $str->get(0, 8);
        self::assertEquals($res, 191);
        $res = $str->get(8, 8);
        self::assertEquals($res, 255);
        $res = $str->get(16, 8);
        self::assertEquals($res, 192);
        $res = $str->get(24, 8);
        self::assertEquals($res, 0);

        $res = $str->get(1, 5);
        self::assertEquals($res, 15);

        $res = $str->get(37, 5);
        self::assertEquals($res, 31);
    }

    public function testBitStringCountNumberOfBitsSetForGivenPosition(): void
    {
        $str = new BitString('v__AAB_BDFHJLNPRTVXZbde');
        $res = $str->count(0, 32);
        self::assertEquals($res, 17);
        $res = $str->count(4, 11);
        self::assertEquals($res, 11);
        $res = $str->count(0, 7);
        self::assertEquals($res, 6);
    }
}
