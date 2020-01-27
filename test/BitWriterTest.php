<?php

namespace Sds\Tests;

use PHPUnit\Framework\TestCase;
use Sds\BitWriter;

class BitWriterTest extends TestCase
{
    public function testCharactersMapAccess(): void
    {
        self::assertEquals(BitWriter::ord('A'), 0);
        self::assertNotEquals(BitWriter::ord('A'), 1);
        self::assertEquals(BitWriter::chr(0), 'A');
        self::assertEquals(BitWriter::chr(1), 'B');
        self::assertNotEquals(BitWriter::chr(1), 'A');
    }

    public function testWriteBitData(): void
    {
        $writer = new BitWriter();
        $writer->write(0, 6);
        self::assertEquals($writer->getData(), 'A');
        $writer->write(26, 6);
        self::assertEquals($writer->getData(), 'Aa');
        $writer->write(21, 6);
        self::assertEquals($writer->getData(), 'AaV');

        $writer = new BitWriter();
        $writer->write(63, 10);
        self::assertEquals($writer->getDebugString(), '0000111111');
        $writer->write(62, 12);
        self::assertEquals($writer->getDebugString(), '0000111111000000111110');

        $writer = new BitWriter();
        $writer->write(341, 16);
        self::assertEquals($writer->getDebugString(), '0000000101010101');
    }
}
