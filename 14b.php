<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " [file] [steps]\n");
}

$lines = array_map('trim', file($argv[1]));
$steps = (int)$argv[2];

$template = $lines[0];

$pairs = [];
for ($i = 0; $i < strlen($template) - 1; $i++) {
    $pair = substr($template, $i, 2);
    $pairs[$pair] = ($pairs[$pair] ?? 0) + 1;
}

$chars = [];
foreach (str_split($template) as $char) {
    $chars[$char] = ($chars[$char] ?? 0) + 1;
}

$rules = [];
foreach (array_slice($lines, 2) as $rule) {
    [$a, $b] = explode(' -> ', $rule);
    $rules[$a] = $b;
}

for ($i = 1; $i <= $steps; $i++) {
    $old_pairs = $pairs;

    foreach ($old_pairs as $pair => $count) {
        if (isset($rules[$pair])) {
            [$a, $b] = str_split($pair);
            $insert = $rules[$pair];
            $pairs[$a . $insert] = ($pairs[$a . $insert] ?? 0) + $count;
            $pairs[$insert . $b] = ($pairs[$insert . $b] ?? 0) + $count;
            $chars[$insert] = ($chars[$insert] ?? 0) + $count;
            $pairs[$pair] -= $count;
        }
    }

    $pairs = array_filter($pairs, fn($pair) => $pair > 0);

    echo "After set $i result is " . (max(array_values($chars)) - min(array_values($chars))) . "\n";
}

