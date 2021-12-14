<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " [file] [steps]\n");
}

$lines = array_map('trim', file($argv[1]));
$steps = (int)$argv[2];

$template = $lines[0];
$rules = [];
foreach (array_slice($lines, 2) as $rule) {
    [$from, $to] = explode(' -> ', $rule);
    $rules[$from] = $to;
}

for ($i = 1; $i <= $steps; $i++) {
    $pos = 0;
    while ($pos < strlen($template)) {
        $seq = substr($template, $pos, 2);
        if (isset($rules[$seq])) {
            $template = substr($template, 0, $pos + 1) . $rules[$seq] . substr($template, $pos + 1);
            $pos++;
        }
        $pos++;
    }
    $counts = count_chars($template, 1);
    asort($counts);
    $result = array_values($counts)[count($counts) - 1] - array_values($counts)[0];
    echo "After step #$i (polymer length " . strlen($template) . "): $result\n";
}

