<?php

/**
 * Bruteforce
 */

if (count($argv) < 3) {
    die('Usage: ' . basename(__FILE__) . " [file] [steps]\n");
}

$steps = intval($argv[2]);

$lines = file($argv[1]);
$algo = str_split(str_replace(['#', '.'], [1, 0], trim($lines[0])));
$image = array_map(fn($line) => array_map(intval(...), str_split(str_replace(['#', '.'], [1, 0], trim($line)))), array_slice($lines, 2));

function dump($image)
{
    echo implode("\n", array_map(implode(...), $image)) . "\n";
}

$hashes = [];
for ($i = 0; $i < 512; $i++) {
    $hashes[substr('00000000' . decbin($i), -9)] = $algo[$i];
}

$background = 0;
for ($i = 1; $i <= $steps; $i++) {
    $prev = microtime(true);
    echo "Ram usage: " . number_format(memory_get_usage() / 1024 / 1024, 1) . "M\n";
    $new_image = [];
    $q = 1;
    $background = ($algo[0] && $i % 2 === 0) ? 1 : 0;
    echo "Step $i with background $background ... ";
    for ($y = -$i, $ymax = count($image) + $i; $y < $ymax; $y++) {
        for ($x = -$i, $xmax = count($image[0]) + $i; $x < $xmax; $x++) {
            $bin = '';
            for ($yy = -1; $yy < 2; $yy++) {
                for ($xx = -1; $xx < 2; $xx++) {
                    $bin .= ($image[$y + $yy][$x + $xx] ?? $background);
                }
            }
            $new_image[$y][$x] = $hashes[$bin];
        }
    }
    $image = $new_image;
    $now = microtime(true);
    echo 'done in ' . number_format($now - $prev, 2) . "s\n";
    $prev = $now;
}

$result = array_sum(array_map(fn($row) => count(array_filter($row, fn($pixel) => $pixel)), $image));
echo "Result: $result\n";