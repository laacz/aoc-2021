<?php

if (count($argv) !== 3) {
    die('Usage: ' . basename(__FILE__) . " file days\n");
}

class Fish
{
    public function __construct(
        public int $counter
    )
    {
    }
}

function dumpState(array $fishes, int $days = 0)
{
    echo "After $days days: " . implode(',', array_map(fn($f) => $f->counter, $fishes)) . "\n";
}

$fishes = array_map(fn($fish) => new Fish((int)$fish), explode(',', file($argv[1])[0]));
$days = (int)$argv[2];
$day = 0;

//dumpState($fishes);
while ($day < $days) {
    foreach ($fishes as $fish) {
        $fish->counter--;
        if ($fish->counter < 0) {
            $fish->counter = 6;
            $fishes[] = new Fish(8);
        }
    }
    $day++;
//    dumpState($fishes, $day);
}

echo "After $days day(s) there would be " . count($fishes) . " fish(es)\n";