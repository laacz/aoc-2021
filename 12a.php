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
function walk($node, $visited = [])
{
    global $graph, $paths;
    $visited[] = $node;
    foreach ($graph[$node] as $child) {
        if (strtoupper($child) === $child || !in_array($child, $visited, true)) {
            walk($child, $visited);
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
