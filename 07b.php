<?php

if (count($argv) !== 2) {
    die('File as param');
}

$positions = array_map(fn($pos) => (int)$pos, explode(',', file($argv[1])[0]));

$probable_alignment_costs = [];

function cost(int $position, int $target): int
{
    $distance = abs($target - $position);
    return $distance > 1 ? $distance * ($distance + 1) / 2 : $distance;
}

for ($target = min($positions); $target <= max($positions); $target++) {
    $probable_alignment_costs[$target] = array_sum(array_map(fn($position) => cost($position, $target), $positions));
}

asort($probable_alignment_costs);

$position = array_key_first($probable_alignment_costs);

echo "Fuel costs to align at position $position are {$probable_alignment_costs[$position]}\n";

