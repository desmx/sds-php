# A Succinct Trie for PHP

PHP Port of Steve Hanov JavaScript library released to the public domain.

The SDS library is designed to work with succinctly encoded trie structure. 
The trie is encoded to a succinct bit string using the method of Jacobson (1989). 

The resulting trie does not have to be decoded to be used. Searching a word in the encoded data can be done in
 O(mlogn) time, where m is the number of symbols in the target word, and n is the number of nodes in the trie.

## Installing SDS Trie

The recommended way to install SDS Trie is through
[Composer](https://getcomposer.org/).

```bash
composer require desmx/sds-php
```

## Quick usage

Suppose we have encoded JSON data:
```json
{
    "nodeCount": 27, 
	"directory": "Bs", 
	"trie": "v___8AAAAfwQxRySzT0U1V2W3X4Y5Z6a7b8cg", 
	"t1size": 1024, 
	"t2size": 32
}
```
We can create succint trie from given json data.
```php
<?php

$json = '{
    "nodeCount": 27, 
    "directory": "Bs", 
    "trie": "v___8AAAAfwQxRySzT0U1V2W3X4Y5Z6a7b8cg", 
    "t1size": 1024, 
    "t2size": 32
}';
$encTrie = \Sds\EncodedTrie::createFromJson($json);
$encTrie->lookup("a");
$encTrie->lookup("word");
```

To create your own tree, you can use a \Sds\Trie class and then encode it into a json

```php
<?php
$trie = new \Sds\Trie();
$trie->insert('worda');
$trie->insert('wordb');
$trie->insert('wordc');
$encTrie = $trie->encode();
// Lookup
$encTrie->lookup('wordb');
// Export to json
echo $encTrie->toJson();
```

