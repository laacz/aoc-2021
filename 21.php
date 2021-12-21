<?php

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

preg_match_all('/(\d+)$/m', file_get_contents($argv[1]), $matches);

$orig_positions = $positions = array_merge($matches[0]);

$scores = [0, 0];

$die = 0;
$win = false;
$rolls = [];

$count = 0;
while (!$win) {
    $count += 3;
    $current = ($count - 1) % 2;
    $rolls = [$die++ % 100 + 1, $die++ % 100 + 1, $die++ % 100 + 1];
    $positions[$current] = ($positions[$current] + array_sum($rolls) - 1) % 10 + 1;
    $scores[$current] += $positions[$current];

    if (max($scores) >= 1000) {
        break;
    }
}

$result1 = min($scores) * $count;
echo "Part 1: " . min($scores) . " * $count = $result1\n";

const COMBOS = [3 => 1, 3, 6, 7, 6, 3, 1];

function play($position1, $position2, $score1 = 0, $score2 = 0)
{
    static $cache = [];

    if ($score1 >= 21) {
        return [1, 0];
    } elseif ($score2 >= 21) {
        return [0, 1];
    }

    $key = "$position1-$position2-$score1-$score2";

    if (!isset($cache[$key])) {
        $result = [0, 0];
        foreach (COMBOS as $roll => $pos_counts) {
            $position = ($position1 + $roll) % 10;
            $split_result = play($position2, $position, $score2, $score1 + $position + 1);

            $result[0] += $split_result[1] * $pos_counts;
            $result[1] += $split_result[0] * $pos_counts;
        }
        $cache[$key] = $result;
    }

    return $cache[$key];
}

$result = play($orig_positions[0] - 1, $orig_positions[1] - 1);
echo "Part 2: max($result[0], $result[1]) = " . max($result) . "\n";
