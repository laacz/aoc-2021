<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

function dump($grid)
{
    echo implode("\n", array_map(fn($row) => implode('', array_map(fn($cell) => $cell ? '#' : '.', $row)), $grid)) . "\n";
}

$folds = $grid = [];
$maxx = $maxy = 0;
foreach (file($argv[1]) as $line) {
    $line = trim($line);
    if (preg_match('/fold.+ ([xy])=(\d+)$/', $line, $matches)) {
        $folds[] = [$matches[1], $matches[2]];
    } else if (str_contains($line, ',')) {
        [$x, $y] = explode(',', $line);
        $maxx = max($maxx, $x);
        $maxy = max($maxy, $y);
        $grid[$y][$x] = 1;
    }
}

for ($y = 0; $y <= $maxy; $y++) {
    for ($x = 0; $x <= $maxx; $x++) {
        if (!isset($grid[$y][$x])) {
            $grid[$y][$x] = 0;
        }
    }
    ksort($grid[$y]);
}
ksort($grid);

foreach ($folds as $instructions) {
    [$axis, $position] = $instructions;
    $position = (int)$position;
    echo "Fold along {$axis}={$position}\n";
    if ($axis === 'y') {
        $maxy = count($grid) - 1;
        for ($y = 0; $y < $position; $y++) {
            foreach ($grid[$y] as $x => $val) {
                $grid[$y][$x] += $grid[$maxy - $y][$x];
            }
        }
        $grid = array_slice($grid, 0, $position);
    } else {
        $maxx = count($grid[0]) - 1;
        foreach ($grid as $y => $row) {
            for ($x = 0; $x < $position; $x++) {
                $grid[$y][$x] += $grid[$y][$maxx - $x];
            }
            $grid[$y] = array_slice($grid[$y], 0, $position);
        }
    }
    $result = count(array_filter(array_merge(...$grid), fn($cell) => $cell !== 0));
    echo "There are $result visible dots\n";
}

dump($grid);