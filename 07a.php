<?php

if (count($argv) !== 2) {
    die('File as param');
}

$positions = array_map(fn($pos) => (int)$pos, explode(',', file($argv[1])[0]));

$probable_alignment_costs = [];
for ($position = min($positions); $position <= max($positions); $position++) {
    $probable_alignment_costs[$position] = array_sum(array_map(fn($pos) => abs($position - $pos), $positions));
}

asort($probable_alignment_costs);

$position = array_key_first($probable_alignment_costs);

echo "Fuel costs to align at position $position are {$probable_alignment_costs[$position]}\n";

