<?php

namespace Sds;

/**
    The EncodedTrie is used for looking up words in the encoded trie.
 */
class EncodedTrie
{
    /**
      A string representing the encoded trie
     */
    private $data;

    /**
      RankDirectory for given data
     */
    private $directory;

    /**
      @var int The number of nodes in the trie
     */
    private $nodeCount;

    /**
      @var int The number of bits that each entry in the Level 1 table
     */
    private $t1size;

    /**
      @var int The number of bits that each entry in the Level 2 table
     */
    private $t2size;

    /**
      @var int The position of the first bit of the data in 0th node. In non-root nodes, this would contain 6-bit chars.
     */
    private $charStart;

    /**
      Create and return EncodedTrie object from given json
  
      @return object encoded trie object
     */
    public static function createFromJson( $json )
    {
        $j = json_decode($json, true);
        if( $j && isset($j["trie"]) && isset($j["directory"]) && isset($j["nodeCount"]) && isset($j["t1size"]) && isset($j["t2size"]) ) {
            return new EncodedTrie( $j["trie"], $j["directory"], $j["nodeCount"], $j["t1size"], $j["t2size"] );
        }
        return null;
    }

    /**
        @param string $data A string representing the encoded trie.

        @param string $directoryData A string representing the RankDirectory.

        @param int $nodeCount The number of nodes in the trie.

        @param t1size The number of bits that each entry in the Level 1 table
        summarizes. This should be a multiple of t2size.

        @param t2size The number of bits that each entry in the Level 2 table
        summarizes.
     */
    public function __construct($data, $directoryData, $nodeCount, $t1size = 32 * 32, $t2size = 32)
    {
        $this->data = new BitString($data);
        $this->directory = new RankDirectory($directoryData, $data, $nodeCount * 2 + 1, $t1size, $t2size);

        $this->nodeCount = $nodeCount;

        // The position of the first bit of the data in 0th node. In non-root
        // nodes, this would contain 6-bit chars.
        $this->charStart = $nodeCount * 2 + 1;
        $this->t1size = $t1size;
        $this->t2size = $t2size;
    }

    /**
       @return string the json string with trie data
     */
    public function toJson()
    {
        return '{"nodeCount":'.$this->nodeCount
             .', "directory": "'.$this->directory->getData().'"'
             .', "trie":"'.$this->data->getData().'"'
             .', "t1size": '.$this->t1size
             .', "t2size": '.$this->t2size
             .'}';
    }

    /**
       Retrieve the EncodedTrieNode of the trie, given its index in level-order.
       This is a private function that you don't have to use.
     */
    private function getNodeByIndex($index)
    {
        // retrieve the 6-bit char.
        $final = 1 === $this->data->get($this->charStart + $index * 6, 1);
        $char = chr($this->data->get($this->charStart + $index * 6 + 1, 5) + ord('a'));
        $firstChild = $this->directory->select(0, $index + 1) - $index;

        // Since the nodes are in level order, this nodes children must go up
        // until the next node's children start.
        $childOfNextNode = $this->directory->select(0, $index + 2) - $index - 1;

        return new EncodedTrieNode(
            $this,
            $index,
            $char,
            $final,
            $firstChild,
            $childOfNextNode - $firstChild
        );
    }

    /**
      Returns the EncodedTrieNode for the given child.

      @param index The 0-based index of the child of this node. For example, if
      the node has 5 children, and you wanted the 0th one, pass in 0.
     */
    private function getNodeChild($node, $childIndex)
    {
        return $this->getNodeByIndex($node->firstChild + $childIndex);
    }

    /**
      Retrieve the root node. You can use this node to obtain all of the other
      nodes in the trie.
     */
    private function getRoot()
    {
        return $this->getNodeByIndex(0);
    }

    /**
      Look-up a word in the trie. Returns true if and only if the word exists
      in the trie.
     */
    public function lookup($word)
    {
        $word = trim($word);
        $node = $this->getRoot();
        for ($i = 0; $i < strlen($word); ++$i) {
            $child = null;
            $j = 0;
            for (; $j < $node->getChildCount(); ++$j) {
                $child = $this->getNodeChild($node, $j);
                if ($child->char === $word[$i]) {
                    break;
                }
            }

            if ($j === $node->getChildCount()) {
                return false;
            }
            $node = $child;
        }

        return $node->final;
    }
}
