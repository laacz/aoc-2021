<?php

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$locations = array_map(fn($line) => str_split(trim($line)), file($argv[1]));

function dump(array $locations)
{
    echo implode("\n", array_map(fn($line) => implode('', $line), $locations)) . "\n\n";
}

function move(array $locations, string $herd): array
{
    $newlocations = array_fill(0, count($locations), array_fill(0, count($locations[0]), '.'));

    foreach ($locations as $y => $row) {
        foreach ($row as $x => $cucumber) {
            if ($cucumber !== $herd) {
                if ($cucumber === '.') {
                    continue;
                }
                $newlocations[$y][$x] = $cucumber;
            } else {
                $newx = $x + ($herd === '>' ? 1 : 0);
                $newy = $y + ($herd === 'v' ? 1 : 0);

                if ($newx >= count($row)) {
                    $newx = 0;
                }

                if ($newy >= count($locations)) {
                    $newy = 0;
                }

                if ($locations[$newy][$newx] !== '.') {
                    $newx = $x;
                    $newy = $y;
                }

                $newlocations[$newy][$newx] = $cucumber;
            }
        }
    }

    return $newlocations;
}

function step(array $locations): array
{
    return move(move($locations, '>'), 'v');
}

$new_locations = $locations;
$locations = [];
$step = 0;

while ($locations !== $new_locations) {
    $step++;
    $locations = $new_locations;
    $new_locations = move(move($locations, '>'), 'v');
    echo "Step #$step done\n";
}
