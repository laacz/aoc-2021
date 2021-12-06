<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = array_map(fn ($line) => $line, file($argv[1]));

$gamma = '';

function mostCommon(int $position, array $lines, int $default): int
{
    $counts = [0 => 0, 1 => 0];
    foreach ($lines as $line) {
        $counts[(int)$line[$position]]++;
    }
    return $counts[0] === $counts[1] ? $default : ($counts[0] > $counts[1] ? 0 : 1);
}

function leastCommon(int $position, array $lines, int $default): int
{
    $counts = [0 => 0, 1 => 0];
    foreach ($lines as $line) {
        $counts[(int)$line[$position]]++;
    }
    return $counts[0] === $counts[1] ? $default : ($counts[0] < $counts[1] ? 0 : 1);
}

$len = strlen(chop($lines[0]));

$o2_lines = $lines;
$co2_lines = $lines;
for ($i = 0; $i < $len; $i++) {
    $val = mostCommon($i, $o2_lines, 1);
    if (count($o2_lines) > 1) {
        $o2_lines = array_filter($o2_lines, fn ($line) => $line[$i] === "$val");
    }
    $val = leastCommon($i, $co2_lines, 0);
    if (count($co2_lines) > 1) {
        $co2_lines = array_filter($co2_lines, fn ($line) => $line[$i] === "$val");
    }
}

if (count($o2_lines) !== 1) {
    print_r($o2_lines);
    die('o2_lines must contain only one value');
}

if (count($co2_lines) !== 1) {
    print_r($co2_lines);
    die('co2_lines must contain only one value');
}

$o2 = bindec(array_pop($o2_lines));
$co2 = bindec(array_pop($co2_lines));
$life_support = $o2 * $co2;

echo "O2 $o2, CO2 $co2, life support $life_support\n";


