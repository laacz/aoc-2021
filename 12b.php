<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " [file] [steps]\n");
}

$graph = [];
foreach (array_map(static fn($line) => explode('-', trim($line)), file($argv[1])) as $v) {
    $graph[$v[0]][] = $v[1];
    $graph[$v[1]][] = $v[0];
}

// Simple DFS
$paths = $visited = [];
function walk($node, $visited = [], $twice = false)
{
    global $graph, $paths;
    $visited[] = $node;

    if ($node !== 'end') {
        foreach ($graph[$node] as $child) {
            $is_uppercase = strtoupper($child) === $child;
            $has_dupe = !$is_uppercase && in_array($child, $visited, true);
            if ($child !== 'start' && ($is_uppercase || !in_array($child, $visited, true) || (!$twice && $has_dupe))) {
                walk($child, $visited, $twice || $has_dupe);
            }
        }
    }

    $paths[] = $visited;
}

walk('start');
$count = 0;
foreach ($paths as $path) {
    if ($path[count($path) - 1] === 'end') {
        $count++;
    }
}

echo "Total possible paths: $count\n";
