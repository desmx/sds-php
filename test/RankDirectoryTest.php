<?php

namespace Sds\Tests;

use PHPUnit\Framework\TestCase;
use Sds\BitString;
use Sds\RankDirectory;

class RankDirectoryTest extends TestCase
{
    private static $encodedTrieString = 'v__AAB_BDFHJLNPRTVXZbde';

    private function createRankDirectory()
    {
        // Test trie with "a".."p" one char items
        $rd = RankDirectory::create(self::$encodedTrieString, 17 * 2 + 1);

        return $rd;
    }

    public function testRankDirectoryFactory(): void
    {
        $rd = $this->createRankDirectory();
        self::assertInstanceOf(RankDirectory::class, $rd);
        self::assertEquals($rd->getData(), 'BE');
    }

    public function testRankDirectoryRankFunction(): void
    {
        $rd = $this->createRankDirectory();
        $bitString = new BitString(self::$encodedTrieString);
        self::assertEquals($rd->rank(1, 8), 8);
        self::assertEquals($rd->rank(1, 26), 17);
        self::assertEquals($rd->rank(1, 8), $bitString->rank(8));
        self::assertEquals($rd->rank(1, 26), $bitString->rank(26));
    }

    public function testRankDirectorySelectFunction(): void
    {
        $rd = $this->createRankDirectory();
        self::assertEquals($rd->select(0, 1), 1);
        self::assertEquals($rd->select(0, 3), 19);
        self::assertEquals($rd->select(0, 7), 23);
        self::assertEquals($rd->select(0, 17), 33);
        self::assertEquals($rd->select(1, 17), 17);
    }
}
