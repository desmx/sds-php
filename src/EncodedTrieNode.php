<?php

namespace Sds;

/**
  This class is used for traversing the succinctly encoded trie.
 */
class EncodedTrieNode
{
    public function __construct($trie, $index, $char, $final, $firstChild, $childCount)
    {
        $this->trie = $trie;
        $this->index = $index;
        $this->char = $char;
        $this->final = $final;
        $this->firstChild = $firstChild;
        $this->childCount = $childCount;
    }

    /**
      Returns the number of children.
     */
    public function getChildCount()
    {
        return $this->childCount;
    }
}
