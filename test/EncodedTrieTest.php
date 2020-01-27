<?php

namespace Sds\Tests;

use PHPUnit\Framework\TestCase;
use Sds\Trie;
use Sds\EncodedTrie;

class EncodedTrieTest extends TestCase
{
    public function testLookupWordInEncodedTrie(): void
    {
        $trie = new Trie();
        foreach (range('a', 'z') as $v) {
            $trie->insert($v);
        }
        $encTrie = $trie->encode();
        self::assertFalse($encTrie->lookup('aa'));
        self::assertTrue($encTrie->lookup('a'));
        self::assertTrue($encTrie->lookup('z'));
        self::assertTrue($encTrie->lookup('a'));
        self::assertTrue($encTrie->lookup('a '));
        self::assertTrue($encTrie->lookup(" a\n"));
    }

    public function testCreateEncodedTrieFromJsonData(): void
    {
        $json = '{"nodeCount": 27, "directory": "Bs", 
                  "trie": "v___8AAAAfwQxRySzT0U1V2W3X4Y5Z6a7b8cg", "t1size": 1024, "t2size": 32}';
        $encTrie = EncodedTrie::createFromJson($json);
        self::assertFalse($encTrie->lookup('aa'));
        self::assertTrue($encTrie->lookup('a'));
        self::assertTrue($encTrie->lookup('z'));
        self::assertTrue($encTrie->lookup('a'));
        self::assertTrue($encTrie->lookup('a '));
        self::assertTrue($encTrie->lookup(" a\n"));
    }

}
