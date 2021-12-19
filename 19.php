<?php
/** @noinspection AutoloadingIssuesInspection */

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$scanners = [];
$scanner = false;
foreach (file($argv[1]) as $line) {
    $line = trim($line);
    if (strlen($line)) {
        if (str_starts_with($line, '---')) {
            $scanner = trim($line, ' -');
            $scanners[$scanner] = [];
        } elseif ($scanner && count($coords = explode(',', $line)) === 3) {
            $scanners[$scanner][] = $coords;
        }
    }
}

function rotate(array $points, int $times_roll, int $times_yaw, int $times_pitch): array
{
    $alpha = $times_yaw * M_PI / 2;
    $beta = $times_pitch * M_PI / 2;
    $gamma = $times_roll * M_PI / 2;
    $ret = [];
    // https://en.wikipedia.org/wiki/Rotation_matrix#General_rotations
    // yaw, pitch, and roll angles are α, β and γ
    $rotation = [
        [
            cos($alpha) * cos($beta),
            sin($alpha) * sin($beta) * sin($gamma) - sin($alpha) * cos($gamma),
            cos($alpha) * sin($beta) * cos($gamma) + sin($alpha) * sin($gamma),
        ],
        [
            sin($alpha) * cos($beta),
            sin($alpha) * sin($beta) * sin($gamma) + cos($alpha) * cos($gamma),
            cos($alpha) * sin($beta) * cos($gamma) + sin($alpha) * sin($gamma),
        ],
        [
            sin($beta),
            cos($beta) * sin($gamma),
            cos($beta) * cos($gamma),
        ],
    ];

    foreach ($points as $point) {
        $x = round($rotation[0][0] * $point[0] + $rotation[0][1] * $point[1] + $rotation[0][2] * $point[2]);
        $y = round($rotation[1][0] * $point[0] + $rotation[1][1] * $point[1] + $rotation[1][2] * $point[2]);
        $z = round($rotation[2][0] * $point[0] + $rotation[2][1] * $point[1] + $rotation[2][2] * $point[2]);
        $ret[] = [$x, $y, $z];
    }

    return $ret;
}

function sorted($points)
{
    $ret = $points;
    usort($ret, function ($a, $b) {
        if (($a[0] <=> $b[0]) === 0) {
            if (($a[1] <=> $b[1]) === 0) {
                return $a[2] <=> $b[2];
            }
            return $a[1] <=> $b[1];
        }
        return $a[0] <=> $b[0];
    });
    return $ret;
}

function vector_map($points1, $points2): array
{
    $ret = [];
    foreach ($points1 as $a) {
        foreach ($points2 as $b) {
            $ret[] = [$a[0] - $b[0], $a[1] - $b[1], $a[2] - $b[2]];
        }
    }
    return $ret;
}

$space = [...$scanners[array_key_first($scanners)]];

$rotations = [
    ['times_roll' => 0, 'times_yaw' => 0, 'times_pitch' => 0],
    ['times_roll' => 0, 'times_yaw' => 0, 'times_pitch' => 1],
    ['times_roll' => 0, 'times_yaw' => 1, 'times_pitch' => 0],
    ['times_roll' => 0, 'times_yaw' => 1, 'times_pitch' => 1],
    ['times_roll' => 1, 'times_yaw' => 0, 'times_pitch' => 0],
    ['times_roll' => 1, 'times_yaw' => 0, 'times_pitch' => 1],
    ['times_roll' => 1, 'times_yaw' => 1, 'times_pitch' => 0],
    ['times_roll' => 1, 'times_yaw' => 1, 'times_pitch' => 1],
];

$matched = $unmatched = [];
foreach ($scanners as $name => $scanner) {
    if (!count($matched)) {
        $matched[$name] = $scanner;
    } else {
        $unmatched[$name] = $scanner;
    }
}

while (count($unmatched)) {
    $found = false;
    foreach ($matched as $name1 => $scanner1) {
        foreach ($unmatched as $name2 => $scanner2) {
            echo "Matcbhing [$name1] with [$name2]\n";

            $roll = 0;
            while (!$found && $roll < 4) {
                $yaw = 0;
                while (!$found && $yaw < 4) {
                    $pitch = 0;
                    while (!$found && $pitch < 4) {
                        $result = rotate($scanner2, $roll, $yaw, $pitch);
                        $map = [];
                        foreach ($scanner1 as $a) {
                            foreach ($result as $b) {
                                $map[] = [$a[0] - $b[0], $a[1] - $b[1], $a[2] - $b[2]];
                            }
                        }

                        $group = [];
                        foreach ($map as $point) {
                            $key = implode(',', array_map(static fn($p) => (string)$p, $point));
                            $group[$key] = ($group[$key] ?? 0) + 1;
                        }

                        $group = array_filter($group, static fn($cnt) => $cnt > 11);

                        if (count($group)) {
                            $vec = array_map(intval(...), explode(',', array_key_first($group)));
                            foreach ($result as $k => $point) {
                                $result[$k] = [$point[0] + $vec[0], $point[1] + $vec[1], $point[2] + $vec[2]];
                            }
                            echo "Match between [$name1] and [$name2] at rotation($roll, $yaw, $pitch) and an offset vector (" . implode(', ', $vec) . ")\n";
                            $matched[$name2] = $result;
                            unset($unmatched[$name2]);
                            $found = true;
                            break;
                        }

                        $pitch++;
                    }
                    $yaw++;
                }
                $roll++;
            }
            if ($found) {
                break;
            }
        }
        if ($found) {
            break;
        }
    }
}
