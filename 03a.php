<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = array_map(fn ($line) => $line, file($argv[1]));

$gamma = '';

$len = strlen(chop($lines[0]));
for ($bit = 0; $bit < $len; $bit++) {
    $counts = [0 => 0, 1 => 0];
    foreach ($lines as $line) {
        if (strlen($line)) {
            $counts[(int)$line[$bit]]++;
        }
    }
    $gamma .= $counts[0] > $counts[1] ? '0' : '1';
}

$epsilon = decbin(bindec($gamma) ^ bindec(str_repeat('1', $len)));

echo "Gamma: $gamma ("  . bindec($gamma) . "), epsilon: $epsilon (" . bindec($epsilon) . "), gamma * epsilon = " . (bindec($gamma) * bindec($epsilon)) . "\n";
