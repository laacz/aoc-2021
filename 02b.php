<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = array_map(fn ($line) => $line, file($argv[1]));

$horizontal = 0;
$depth = 0;
$aim = 0;
echo "horizontal $horizontal, depth $depth, aim $aim\n";
foreach ($lines as $line) {
    list($cmd, $val) = explode(' ', $line);
    $val = (int)$val;
    $aim += match ($cmd) {
        'down' => $val,
        'up' => -1 * $val,
        default => 0,
    };
    $horizontal += match ($cmd) {
        'forward' => $val,
        default => 0,
    };
    $depth += match ($cmd) {
        // 'down' => $val,
        // 'up' => -1 * $val,
        'forward' => $aim * $val,
        default => 0,
    };
    echo "horizontal $horizontal, depth $depth, aim $aim\n";
}

echo "$horizontal * $depth = " . ($horizontal * $depth) . "\n";
