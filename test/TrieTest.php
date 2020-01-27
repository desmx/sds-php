<?php

namespace Sds\Tests;

use PHPUnit\Framework\TestCase;
use Sds\EncodedTrie;
use Sds\Trie;

class TrieTest extends TestCase
{
    public function testInsertItemsAndCheckNodeCount(): void
    {
        $trie = new Trie();
        $trie->insert('aaa');
        self::assertEquals($trie->getNodeCount(), 4);
        $trie->insert('aab');
        self::assertEquals($trie->getNodeCount(), 5);
    }

    public function testTrieEncoding(): void
    {
        $trie = new Trie();
        $trie->insert('aaa');
        self::assertEquals($trie->getNodeCount(), 4);
        $encTrie = $trie->encode();
        self::assertInstanceOf(EncodedTrie::class, $encTrie);
    }

    public function testCheckTrieEncodingResult(): void
    {
        $trie = new Trie();
        $trie->insert('aa');
        $encTrie = $trie->encode();
        self::assertJsonStringEqualsJsonString(
            $encTrie->toJson(),
            '{"nodeCount": 3, "directory": "", "trie": "qfgQA", "t1size": 1024, "t2size": 32}'
        );
    }

    public function testCheckTrieEncodingResultWithDirectory(): void
    {
        $trie = new Trie();
        foreach (range('a', 'z') as $v) {
            $trie->insert($v);
        }
        $encTrie = $trie->encode();
        self::assertJsonStringEqualsJsonString(
            $encTrie->toJson(),
            '{"nodeCount": 27, "directory": "Bs", 
              "trie": "v___8AAAAfwQxRySzT0U1V2W3X4Y5Z6a7b8cg", "t1size": 1024, "t2size": 32}'
        );
    }
}
