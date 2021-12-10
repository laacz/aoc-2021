<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

$lines = array_map(trim(...), file($argv[1]));

$combos = [')' => '(', ']' => '[', '}' => '{', '>' => '<'];
$points = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];
$total = 0;

foreach ($lines as $line) {
    $open = '';
    $char = $expected = false;

    for ($i = 0; $i < strlen($line); $i++) {
        $char = $line[$i];
        if (in_array($char, $combos)) {
            $open .= $char;
        } else if (substr($open, -1) !== $combos[$char]) {
            $expected = array_search(substr($open, -1), $combos);
            break;
        } else {
            $open = substr($open, 0, -1);
        }
    }

    echo "$line => ";
    if ($expected) {
        echo "expected $expected, got $char\n";
        $total += $points[$char];
    } else {
        echo "OK\n";
    }
}

echo "Syntax score is $total\n";