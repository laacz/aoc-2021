<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = file($argv[1]);

$map = [];

$maxx = $maxy = 0;
$minx = $miny = 99999999;

foreach ($lines as $line) {
    if (!preg_match('/^(\d+),(\d+) -> (\d+),(\d+)/', $line, $matches)) {
        continue;
    }
    [$x1, $y1, $x2, $y2] = [$matches[1], $matches[2], $matches[3], $matches[4]];

//    for ($y = $miny; $y <= $maxy; $y++) {
//        for ($x = $minx; $x <= $maxx; $x++) {
//            echo $map[$y][$x] ?? '.';
//        }
//        echo "\n";
//    }

    // Draw a line. Works OK only when at 0°, 45° or 90°, but that's inferred from input data.
    $steps = max(abs($x2 - $x1), abs($y2 - $y1));
    for ($step = 0; $step <= $steps; $step++) {
        $x = $x1 + (int)($step * ($x2 - $x1) / $steps);
        $y = $y1 + (int)($step * ($y2 - $y1) / $steps);

        $map[$y][$x] = isset($map[$y][$x]) ? $map[$y][$x] + 1 : 1;
    }

    // Just to get bounds of the map
    $maxx = max($maxx, $x1, $x2);
    $maxy = max($maxy, $y1, $y2);
    $minx = min($minx, $x1, $x2);
    $miny = min($miny, $y1, $y2);
}

// Now iterate through all the points and count dangerous ones.
$val = 0;
for ($y = $miny; $y <= $maxy; $y++) {
    for ($x = $minx; $x <= $maxx; $x++) {
//        echo $map[$y][$x] ?? '.';
        $val += ($map[$y][$x] ?? 0) > 1 ? 1 : 0;
    }
//    echo "\n";
}

echo "Dangerous: $val\n";
