<?php

# I hate graphs. Naive solution. Solves the part 2 in under 1 week.

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$map = array_map(fn($line) => str_split(trim($line)), file($argv[1]));

$max = max(array_merge(...$map)) + 1;

function shortest_path($grid)
{
    $distances = array_map(fn($row) => array_fill(0, count($row), INF), $grid);
    $directions = [
        [-1, 0],
        [0, 1],
        [1, 0],
        [0, -1],
    ];
    $visited = [[0, 0, 0]];
    $distances[0][0] = 0;

    while (count($visited) > 0) {
        $k = array_shift($visited);
        foreach ($directions as $dir) {
            $x = $k[0] + $dir[0];
            $y = $k[1] + $dir[1];
            if ($x < 0 || $y < 0 || $x >= count($grid) || $y >= count($grid[0])) {
                continue;
            }
            if ($distances[$x][$y] > $distances[$k[0]][$k[1]] + $grid[$x][$y]) {
                $distances[$x][$y] = $distances[$k[0]][$k[1]] + $grid[$x][$y];
                $visited[] = [$x, $y, $distances[$x][$y]];
            }
        }
        usort($visited, fn($a, $b) => $a[2] <=> $b[2]);
    }
    return $distances[count($grid) - 1][count($grid[0]) - 1];
}

echo 'Part 1: ' . shortest_path($map) . "\n";

$new_map = $map;
$height = count($map);
$width = count($map[0]);

for ($yy = 0; $yy < 5; $yy++) {
    for ($xx = 0; $xx < 5; $xx++) {
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $newval = $map[$y][$x] + $xx + $yy;
                $map[$yy * $height + $y][$xx * $width + $x] = $newval > 9 ? $newval - 9 : $newval;
            }
        }
    }
}

echo 'Part 2: ' . shortest_path($map) . "\n";
