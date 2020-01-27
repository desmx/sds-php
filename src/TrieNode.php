<?php

namespace Sds;

class TrieNode
{
    public $char;
    public $final = false;
    public $children = [];

    public function __construct($char)
    {
        $this->char = $char;
        $this->final = false;
        $this->children = [];
    }
}
