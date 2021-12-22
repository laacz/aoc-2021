<?php

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

$data = file_get_contents($argv[1]);
preg_match_all('/^(on|off) x=([-\d]+)\.\.([-\d]+),y=([-\d]+)\.\.([-\d]+),z=([-\d]+)\.\.([-\d]+)/ms', $data, $matches);

$instructions = [];
foreach ($matches[0] as $k => $v) {
    $instructions[$k] = [
        'state' => $matches[1][$k],
        'x' => [intval($matches[2][$k]), intval($matches[3][$k])],
        'y' => [intval($matches[4][$k]), intval($matches[5][$k])],
        'z' => [intval($matches[6][$k]), intval($matches[7][$k])],
    ];
}

function initialize(array $steps): array
{
    $reactor = [];

    foreach ($steps as $cuboid) {
        for ($x = max(-50, $cuboid['x'][0]); $x <= min(50, $cuboid['x'][1]); $x++) {
            for ($y = max(-50, $cuboid['y'][0]); $y <= min(50, $cuboid['y'][1]); $y++) {
                for ($z = max(-50, $cuboid['z'][0]); $z <= min(50, $cuboid['z'][1]); $z++) {
                    $reactor[$x][$y][$z] = $cuboid['state'];
                }
            }
        }
    }

    return $reactor;
}

function intersect(array $a, array $b): ?array
{
    // Separate axis theorem
    if (($a['x'][0] > $b['x'][1] || $a['x'][1] < $b['x'][0]) ||
        ($a['y'][0] > $b['y'][1] || $a['y'][1] < $b['y'][0]) ||
        ($a['z'][0] > $b['z'][1] || $a['z'][1] < $b['z'][0])) {
        return null;
    }

    return [
        'x' => [max($a['x'][0], $b['x'][0]), min($a['x'][1], $b['x'][1])],
        'y' => [max($a['y'][0], $b['y'][0]), min($a['y'][1], $b['y'][1])],
        'z' => [max($a['z'][0], $b['z'][0]), min($a['z'][1], $b['z'][1])],
    ];
}

function split(array $a, array $b): array
{
    if (!($c = intersect($a, $b))) {
        return [$a];
    }

    // At most split in six parts
    $ret = [
        ['x' => $a['x'], 'y' => $a['y'], 'z' => [$a['z'][0], $c['z'][0] - 1]],
        ['x' => $a['x'], 'y' => $a['y'], 'z' => [$c['z'][1] + 1, $a['z'][1]]],
        ['x' => [$a['x'][0], $c['x'][0] - 1], 'y' => $a['y'], 'z' => $c['z']],
        ['x' => [$c['x'][1] + 1, $a['x'][1]], 'y' => $a['y'], 'z' => $c['z']],
        ['x' => $c['x'], 'y' => [$a['y'][0], $c['y'][0] - 1], 'z' => $c['z']],
        ['x' => $c['x'], 'y' => [$c['y'][1] + 1, $a['y'][1]], 'z' => $c['z']],
    ];

    return array_filter($ret, fn($c) => $c['x'][0] <= $c['x'][1] && $c['y'][0] <= $c['y'][1] && $c['z'][0] <= $c['z'][1]);
}

function reboot(array $steps): array
{
    $cuboids = [];

    foreach ($steps as $kk => $step) {

        /**
         * New list is a blank list.
         * We're interested only in 'on' parallelograms only.
         * Split all existing parallelograms into 6 new ones, effectively removing area mentioned in the instructions.
         * Then, add these 6 new parallelograms to the new list
         * If and only if current instruction is 'on', add its parallelogram to the list.
         */

        $new = [];

        foreach ($cuboids as $cuboid) {
            $new = array_merge($new, split($cuboid, $step));
        }

        if ($step['state'] === 'on') {
            $new[] = $step;
        }

        $cuboids = $new;
    }

    return $cuboids;
}

// Brute force, of course
$reactor = initialize($instructions);

while (true) {
    if (!is_array($reactor[array_key_first($reactor)])) {
        break;
    }
    $reactor = array_merge(...$reactor);
}
$result1 = count(array_filter($reactor, fn($v) => $v === 'on'));

// Not brute force, of course
$rebooted = reboot($instructions);

$result2 = array_sum(array_map(fn($c) => (abs($c['x'][1] - $c['x'][0]) + 1) * (abs($c['y'][1] - $c['y'][0]) + 1) * (abs($c['z'][1] - $c['z'][0]) + 1), $rebooted));

echo "Part 1: {$result1}\n";
echo "Part 2: {$result2} in " . count($rebooted) . " cubes\n";
