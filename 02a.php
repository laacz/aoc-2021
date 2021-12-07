<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = array_map(fn($line) => $line, file($argv[1]));

$horizontal = 0;
$depth = 0;
$aim = 0;
foreach ($lines as $line) {
    list($cmd, $val) = explode(' ', $line);
    $horizontal += match($cmd) {
        'forward' => (int)$val,
        default => 0,
    };
    $depth += match($cmd) {
        'down' => (int)$val,
        'up' => -1*(int)$val,
        default => 0,
    };
}

echo "$horizontal * $depth = " . ($horizontal * $depth) . "\n";