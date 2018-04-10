<?php

use Goodby\CSV\Import\Standard\LexerConfig;

$config = new LexerConfig();
$config
    ->setDelimiter("") // Customize delimiter. Default value is comma(,)
    ->setEnclosure("")  // Customize enclosure. Default value is double quotation(")
    ->setEscape("\\")    // Customize escape character. Default value is backslash(\)
    ->setToCharset('UTF-8') // Customize target encoding. Default value is null, no converting.
    ->setFromCharset('SJIS-win') // Customize CSV file encoding. Default value is null.
;
