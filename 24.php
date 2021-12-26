<?php

// Lifted from https://old.reddit.com/r/adventofcode/comments/rnejv5/2021_day_24_solutions/hpuu3e0/

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$commands = array_map(trim(...), file($argv[1]));

function guess(array $digits, array $commands): array
{
    $inp = $digits;
    $subroutine_length = 18;
    $magic_numbers_positions = [4, 5, 15];
    $stack = [];
    foreach (range(0, 13) as $i) {
        [$div, $chk, $add] = array_map(fn($x) => (int)preg_replace('/^.+\s+([-\d]+)$/', '\1', $commands[$i * $subroutine_length + $x]), $magic_numbers_positions);
        if ($div === 1) {
            $stack[] = [$i, $add];
        } elseif ($div === 26) {
            [$j, $add] = array_pop($stack);
            $inp[$i] = $inp[$j] + $add + $chk;
            if ($inp[$i] > 9) {
                $inp[$j] -= ($inp[$i] - 9);
                $inp[$i] = 9;
            }
            if ($inp[$i] < 1) {
                $inp[$j] += (1 - $inp[$i]);
                $inp[$i] = 1;
            }
        }
    }
    return $inp;
}

echo "Result 1: " . implode("", guess(str_split('99999999999999'), $commands)) . "\n";
echo "Result 2: " . implode("", guess(str_split('11111111111111'), $commands)) . "\n";