<?php

$lines = array_map(fn($line) => (int)$line, file('01.txt'));

$prev = null;
$inc = 0;
foreach ($lines as $line) {
    if ($prev !== null) {
        $inc += $line > $prev ? 1 : 0;
    }
    $prev = $line;
}

echo "$inc\n";