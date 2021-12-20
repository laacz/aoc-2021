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

$background = 0;
for ($i = 1; $i <= $steps; $i++) {
    echo "Ram usage: " . number_format(memory_get_usage()/1024/1024, 1) . "M\n";
    $new_image = [];
    $background = ($algo[0] && $i % 2 === 0) ? 1 : 0;
    echo "Step $i with background $background\n";
    for ($y = -$i, $ymax = count($image) + $i; $y < $ymax; $y++) {
        for ($x = -$i, $xmax = count($image[0]) + $i; $x < $xmax; $x++) {
            $bit = 256;
            $b = 0;
            for ($yy = -1; $yy < 2; $yy++) {
                for ($xx = -1; $xx < 2; $xx++) {
                    $b += $bit * ($image[$y + $yy][$x + $xx] ?? $background);
                    $bit = intval($bit / 2);
                }
            }
            $new_image[$y][$x] = $algo[$b];
        }
    }
    $image = $new_image;
}

$result = array_sum(array_map(fn($row) => count(array_filter($row, fn($pixel) => $pixel)), $image));
echo "Result: $result\n";