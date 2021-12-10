<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

$lines = array_map(fn($line) => array_map(intval(...), str_split(trim($line))), file($argv[1]));

$points = [];
foreach ($lines as $y => $line) {
    foreach ($line as $x => $value) {
        if (($lines[$y][$x - 1] ?? 11) > $value &&
            ($lines[$y - 1][$x] ?? 11) > $value &&
            ($lines[$y][$x + 1] ?? 11) > $value &&
            ($lines[$y + 1][$x] ?? 11) > $value) {
            $points[] = $value + 1;
        }
    }
}

$risk_level = array_sum($points);

echo "Low points: $risk_level\n";
