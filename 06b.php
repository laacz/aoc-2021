<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

function dumpState(array $fishcounts, int $days = 0)
{
    echo "After $days day(s): " . array_sum($fishcounts) . " fish(es)\n";
}

$fishcounts = [];
foreach (explode(',', file($argv[1])[0]) as $fish) {
    $fish = (int)$fish;
    $fishcounts[$fish] = ($fishcounts[$fish] ?? 0) + 1;
};

$days = (int)$argv[2];

$day = 0;

//dumpState($fishcounts);
while ($day < $days) {

    $tmp = [];
    foreach ($fishcounts as $counter => $count) {
        $tmp[$counter - 1] = $count;
    }
    $fishcounts = $tmp;

    if (isset($fishcounts[-1])) {
        $fishcounts[6] = ($fishcounts[6] ?? 0) + $fishcounts[-1];
        $fishcounts[8] = ($fishcounts[8] ?? 0) + $fishcounts[-1];
        unset($fishcounts[-1]);
    }

    $day++;
//    dumpState($fishcounts, $day);
}

echo "After $days day(s) there would be " . array_sum($fishcounts) . " fish(es)\n";