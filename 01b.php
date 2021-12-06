<?php

$lines = array_map(fn($line) => (int)$line, file('01.txt'));

$prev = null;
$inc = 0;
$windows = [];
for ($i = 2; $i < count($lines); $i++) {
    $windows[] = $lines[$i-2] + $lines[$i-1] + $lines[$i];
}

foreach ($windows as $line) {
    echo $line . "\n";
    if ($prev !== null) {
        $inc += $line > $prev ? 1 : 0;
    }
    $prev = $line;
}

echo "\n$inc\n";