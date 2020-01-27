<?php

namespace Sds;

class Trie
{
    public $previousWord;
    public $root;
    public $cache;
    public $nodeCount;

    public function __construct()
    {
        $this->previousWord = '';
        $this->root = new TrieNode(' ');
        $this->cache = [$this->root];
        $this->nodeCount = 1;
    }

    /**
      Returns the number of nodes in the trie
     */
    public function getNodeCount()
    {
        return $this->nodeCount;
    }

    /**
     Inserts a word into the trie. This function is fastest if the words are
     inserted in alphabetical order.
     */
    public function insert($word)
    {
        if (!preg_match('/[a-z]+/', $word)) {
            return false;
        }
        $commonPrefix = 0;
        for ($i = 0; $i < min(strlen($word), strlen($this->previousWord)); ++$i) {
            if ($word[$i] !== $this->previousWord[$i]) {
                break;
            }
            ++$commonPrefix;
        }

        $this->cache = array_slice($this->cache, 0, $commonPrefix + 1);
        $node = $this->cache[sizeof($this->cache) - 1];
        //$node = $this->cache[ $commonPrefix ];

        for ($i = $commonPrefix; $i < strlen($word); ++$i) {
            $next = new TrieNode($word[$i]);
            ++$this->nodeCount;
            $node->children[] = $next;
            $this->cache[] = $next;
            $node = $next;
        }

        $node->final = true;
        $this->previousWord = $word;

        return true;
    }

    private function apply($fn)
    {
        $level = [$this->root];
        while (sizeof($level) > 0) {
            $node = array_shift($level);
            for ($i = 0; $i < sizeof($node->children); ++$i) {
                $level[] = $node->children[$i];
            }
            $fn($node);
        }
    }

    private function encodeData()
    {
        // Write the unary encoding of the tree in level order.
        $bitWriter = new BitWriter();
        $bitWriter->write(0x02, 2);
        $this->apply(function ($node) use ($bitWriter) {
            for ($i = 0; $i < sizeof($node->children); ++$i) {
                $bitWriter->write(1, 1);
            }
            $bitWriter->write(0, 1);
        });
        // Write the data for each node, using 6 bits for node. 1 bit stores
        // the "final" indicator. The other 5 bits store one of the 26 chars
        // of the alphabet.
        $a = ord('a');
        $this->apply(function ($node) use ($bitWriter, $a) {
            $value = ord($node->char) - $a;
            if ($node->final) {
                $value |= 0x20;
            }
            $bitWriter->write($value, 6, true);
        });

        return $bitWriter->getData();
    }

    public function encode()
    {
        $data = $this->encodeData();
        $directory = RankDirectory::create($data, $this->getNodeCount() * 2 + 1, 32 * 32, 32);
        $encTrie = new EncodedTrie($data, $directory->getData(), $this->getNodeCount());

        return $encTrie;
    }
}
