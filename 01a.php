<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = array_map(fn($line) => (int)$line, file($argv[1]));

$prev = null;
$inc = 0;
foreach ($lines as $line) {
    if ($prev !== null) {
        $inc += $line > $prev ? 1 : 0;
    }
    $prev = $line;
}

echo "$inc\n";