<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

$lines = array_map(fn($line) => array_map('intval', str_split(trim($line))), file($argv[1]));

/**
 * Outputs extended version of the floor of the caves (with basin id's)
 */
function dump_padded(array $lines): void
{
    echo implode("\n", array_map(fn($line) => implode(' ', array_map(fn($value) => str_pad($value, 3, ' ', STR_PAD_LEFT), $line)), $lines)) . "\n";
}

/**
 * Outputs compact version of the floor of the caves (just boundaries of the basins)
 */
function dump_compact(array $lines): void
{
    echo implode("\n", array_map(fn($line) => implode('', array_map(fn($value) => $value > 9 ? '.' : '#', $line)), $lines)) . "\n";
}

/**
 * Performs simple recursion based flood fill. Input data size is small enough for this to pass.
 */
function floodFill(int $y, int $x, array $lines, int $fill): array
{
    $positions = [[$y - 1, $x], [$y + 1, $x], [$y, $x - 1], [$y, $x + 1]];
    foreach ($positions as $position) {
        if (($lines[$position[0]][$position[1]] ?? 9) < 9) {
            $lines[$position[0]][$position[1]] = $fill;
            $lines = floodFill($position[0], $position[1], $lines, $fill);
        }
    }
    return $lines;
}

// Now iterate over the lines until there are no more basins to fill
$fill = 10;
while (true) {
    $filled = $break = false;
    foreach ($lines as $y => $line) {
        foreach ($line as $x => $value) {
            if ($value < 9) {
                $filled = true;
                $lines = floodFill($y, $x, $lines, $fill);
                $break = true;
                $fill++;
                break;
            }
        }
        if ($break) {
            break;
        }
    }
    if (!$filled) break;
}

dump_compact($lines);

// Create basin size array
$flat_lines = array_merge(...$lines);
$basin_sizes = [];
for ($i = 10; $i < $fill; $i++) {
    $basin_sizes[$i] = count(array_filter($flat_lines, fn($value) => $value === $i));
}

// Find three largest basins and calculate the result
arsort($basin_sizes);
$result = 1;
$i = 0;
foreach ($basin_sizes as $fill => $size) {
    if ($i++ > 2) {
        break;
    }
    echo "Basin filled with " . $fill . " has size of $size\n";
    $result *= $size;
}

echo "Result is $result\n";

