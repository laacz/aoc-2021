<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$input = trim(file($argv[1])[0]);

preg_match('/x=(-?\d+)\.\.(-?\d+), y=(-?\d+)\.\.(-?\d+)/', $input, $matches);
[, $x1, $x2, $y1, $y2] = $matches;

const MAX = 1000;

function shoot($dx, $dy, $target)
{
    $maxy = -MAX;
    $x = $y = 0;
    while ($x <= $target[1] && $y >= $target[2]) {
        $maxy = max($maxy, $y);
        if ($x >= $target[0] && $y <= $target[3]) {
            return $maxy;
        }
        $x += $dx;
        $y += $dy;
        $dx += $dx > 0 ? -1 : 0;
        $dy -= 1;
    }
    return false;
}

# Went the paper road for part 1
$result1 = abs($y1) * (abs($y1) - 1) / 2;
echo 'Part 1: ' . $result1 . "\n";

# Went the brute force road for part 2
$results = [];
foreach (range($y1, MAX) as $dy) {
    foreach (range(0, $x2) as $dx) {
        if (($result = shoot($dx, $dy, [$x1, $x2, $y1, $y2])) !== false) {
            $results[] = $result;
        }
    }
}

echo 'Part 2: ' . count($results) . " (validate formula: part 1 should be " . max($results) . ")\n";
