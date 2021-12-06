<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

function dumpState(array $fishes, int $days = 0)
{
    echo "After $days days: " . implode(',', $fishes) . "\n";
}

$fishes = array_map(fn($fish) => (int)$fish, explode(',', file($argv[1])[0]));
$days = (int)$argv[2];
$day = 0;

//dumpState($fishes);
while ($day < $days) {
    foreach ($fishes as $fish_index => $fish) {
        $fishes[$fish_index]--;
        if ($fishes[$fish_index] < 0) {
            $fishes[$fish_index] = 6;
            $fishes[] = 8;
        }
    }
    $day++;
//    dumpState($fishes, $day);
}

echo "After $days day(s) there would be " . count($fishes) . " fish(es)\n";