<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

$lines = array_map(trim(...), file($argv[1]));

$combos = [')' => '(', ']' => '[', '}' => '{', '>' => '<'];
$points = [')' => 1, ']' => 2, '}' => 3, '>' => 4];
$scores = [];

foreach ($lines as $line) {
    $open = '';
    $char = $not_found = false;

    for ($i = 0; $i < strlen($line); $i++) {
        $char = $line[$i];
        if (in_array($char, $combos)) {
            $open .= $char;
        } else if (substr($open, -1) !== $combos[$char]) {
            $not_found = in_array(substr($open, -1), $combos);
            break;
        } else {
            $open = substr($open, 0, -1);
        }
    }

    if ($not_found === false) {
        $closing_string = implode(array_map(fn($ch) => array_search($ch, $combos), str_split(strrev($open))));
        $score = 0;
        foreach (str_split($closing_string) as $char) {
            $score = $score * 5 + $points[$char];
        }
        echo $closing_string . " with a score of $score\n";
        $scores[] = $score;
    }
}

sort($scores);

echo "Total score is " . $scores[count($scores)/2] . "\n";
