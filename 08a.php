<?php

if (count($argv) !== 2) {
    die('File as param');
}

# Let's go wild with array functions!

$outputs = array_map(fn($line) => explode(' ', preg_replace('/^.+\| /', '', trim($line))), file($argv[1]));

$count = array_reduce($outputs, fn($carry, $output) => $carry + count(array_filter($output, fn($el) => in_array(strlen($el), [2, 3, 4, 7]))), 0);

echo "1, 4, 7, or 8 apper $count time(s)\n";
