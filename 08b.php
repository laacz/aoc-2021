<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = file($argv[1]);

function intersects(string $a, string $b): int
{
    return count(array_intersect(str_split($a), str_split($b)));
}

function filterByLength(array $patterns, int $len): array
{
    return array_filter($patterns, fn($pattern) => strlen($pattern) === $len);
}

function sortedString(string $str): string
{
    $ret = str_split($str);
    sort($ret);
    return implode('', $ret);
}

$total = 0;
foreach ($lines as $line) {
    $line = trim($line);
    list($patterns, $output) = explode(' | ', $line);

    $patterns = array_map(sortedString(...), explode(' ', $patterns));
    $output = array_map(sortedString(...), explode(' ', $output));

    $digits = [
        1 => current(filterByLength($patterns, 2)),
        7 => current(filterByLength($patterns, 3)),
        4 => current(filterByLength($patterns, 4)),
        8 => current(filterByLength($patterns, 7)),
    ];

    # 0, 6, 9
    $digits[0] = current(array_filter(filterByLength($patterns, 6), fn($pattern) => 6 === intersects($pattern, $digits[4]) + intersects($pattern, $digits[7])));
    $digits[6] = current(array_filter(filterByLength($patterns, 6), fn($pattern) => 2 === intersects($pattern, $digits[7])));
    $digits[9] = current(array_filter(filterByLength($patterns, 6), fn($pattern) => !in_array($pattern, $digits)));

    # 2, 3, 5
    $digits[5] = current(array_filter(filterByLength($patterns, 5), fn($pattern) => 5 === intersects($pattern, $digits[7]) + intersects($pattern, $digits[4])));
    $digits[2] = current(array_filter(filterByLength($patterns, 5), fn($pattern) => 4 === intersects($pattern, $digits[7]) + intersects($pattern, $digits[4])));
    $digits[3] = current(array_filter(filterByLength($patterns, 5), fn($pattern) => !in_array($pattern, $digits)));

    if (strlen(implode($digits)) !== 49) {
        echo "$line\n";
        print_r($digits);
        die('Could not deduce all digits!');
    }

    $number = 0;
    foreach (array_reverse($output) as $k => $digit) {
        $number += 10 ** $k * array_search($digit, $digits);
    }
    $total += $number;

    echo "$line = $number\n";
}

echo "Sum of all the numbers: $total\n";