<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " [file] [steps]\n");
}

$grid = array_map(static fn($line) => array_map('intval', str_split(trim($line))), file($argv[1]));

function dump(array $grid, string $msg = ""): void
{
    if ($msg) {
        echo "$msg\n";
    };
    foreach ($grid as $row) {
        foreach ($row as $cell) {
            if ($cell === 0) {
                echo "\e[1;37m";
            }
            echo $cell;
            echo "\e[0m";
        }
        echo "\n";
    }
    echo "\n";
}

function flash(array &$grid): void
{
    $adjacents = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];

    foreach ($grid as $y => $row) {
        foreach ($row as $x => $cell) {
            if ($grid[$y][$x] > 9) {
                $grid[$y][$x] = 0;
                foreach ($adjacents as $adj) {
                    if (isset($grid[$y + $adj[0]][$x + $adj[1]]) && $grid[$y + $adj[0]][$x + $adj[1]] > 0) {
                        $grid[$y + $adj[0]][$x + $adj[1]]++;
                    }
                }
            }
        }
    }
}

function step(array &$grid): int
{
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $cell) {
            $grid[$y][$x]++;
        }
    }

    $flashes = 0;
    while (count(array_filter(array_merge(...$grid), static fn($octopus) => $octopus > 9))) {
        flash($grid);
    }

    $flashes += count(array_filter(array_merge(...$grid), static fn($octopus) => $octopus === 0));
    return $flashes;
}

dump($grid, 'At the start');
$steps = (int)$argv[2];
$flashes = 0;
for ($step = 1; $step <= $steps; $step++) {
    $flashes = step($grid);
    dump($grid, "After step #$step");
    if ($flashes === 100) {
        echo "Synced after step #$step\n";
        break;
    }
}

