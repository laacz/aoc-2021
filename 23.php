<?php

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file]\n");
}

# Let's assume that an amphipod can have only two moves.
# * Move from the room into the hallway and wait.
# * Move from the hallway into its designated room, if it's not occupaid by an amphipod which belongs to the other room.

$rooms = [];
$hallway = '-1';
foreach (file($argv[1]) as $line) {
    $line = trim($line);
    if (preg_match('/([A-D.])#([A-D.])#([A-D.])#([A-D.])/', $line, $matches)) {
        for ($i = 1; $i < 5; $i++) {
            $rooms[$i - 1] = ($rooms[$i - 1] ?? '') . $matches[$i];
        }
    } else if (preg_match('/^#([^#]+)#$/', $line, $matches)) {
        $hallway = $matches[1];
    }
}

$state = [
    'rooms' => $rooms,
    'hallway' => $hallway,
    'cost' => 0,
];

const AMPHIPODS = [
    'A' => 1,
    'B' => 10,
    'C' => 100,
    'D' => 1000,
];

const ROOMS = [
    'A' => 0,
    'B' => 1,
    'C' => 2,
    'D' => 3,
];

const WAITING_AREAS = [0, 1, 3, 5, 7, 9, 10];

define("ROOM_DEPTH", strlen($state['rooms'][0]));

function is_win($state): bool
{
    return $state['rooms'][0] === str_repeat('A', ROOM_DEPTH) &&
        $state['rooms'][1] === str_repeat('B', ROOM_DEPTH) &&
        $state['rooms'][2] === str_repeat('C', ROOM_DEPTH) &&
        $state['rooms'][3] === str_repeat('D', ROOM_DEPTH);
}

// Hashes state to string
function state_to_string($state): string
{
    return $state['hallway'] . implode('', $state['rooms']);
}

// Shows current state
function dump_state($state)
{
    $hallway = str_replace('.', ' ', $state['hallway']);
    echo <<<STR
    #############
    #{$hallway}#
    ##
    STR;
    foreach (str_split($state['rooms'][0]) as $k => $v) {
        echo $k === 0 ? "" : "  ";
        foreach ($state['rooms'] as $room) {
            echo '#' . str_replace('.', ' ', $room[$k]);
        }
        echo $k === 0 ? '###' : '#';
        echo "\n";
    }
    echo "  #########\n";
}

function get_moves($state): array
{
    $ret = [];

    // Generates all legal moves from the hallway into the rooms
    foreach (str_split($state['hallway']) as $pos => $occ) {
        if ($occ !== '.' && in_array($pos, WAITING_AREAS, true)) {
            // Room is ready only if there are no other species of Amphipods there
            $room_is_ready = str_replace(['.', $occ], '', $state['rooms'][ROOMS[$occ]]) === '';

            $room_pos = ROOMS[$occ] * 2 + 2;
            $pos_from = $pos > $room_pos ? $room_pos : $pos + 1;

            // Clear path consists of dots only
            $path = substr($state['hallway'], $pos_from, abs($room_pos - $pos));
            $path_is_clear = str_replace('.', '', $path) === '';

            if ($room_is_ready && $path_is_clear) {
                $new_state = $state;
                $new_state['rooms'][ROOMS[$occ]] = substr($new_state['rooms'][ROOMS[$occ]] . $occ, -ROOM_DEPTH);
                $new_state['hallway'] = substr($new_state['hallway'], 0, $pos) . '.' . substr($new_state['hallway'], $pos + 1);

                $path_length = strlen($path) + ROOM_DEPTH - strlen(str_replace('.', '', $state['rooms'][ROOMS[$occ]]));

                $ret[] = ['state' => $new_state, 'cost' => $state['cost'] + AMPHIPODS[$occ] * $path_length];
            }
        }
    }

    // move out of the rooms
    foreach (ROOMS as $amphipod => $room) {
        $room_pos = ROOMS[$amphipod] * 2 + 2;

        // Room is empty
        if ($state['rooms'][$room] === str_repeat('.', ROOM_DEPTH)) {
            continue;
        }

        // Probably it would be much more efficient to walk left and right from the room.
        // But I have enough resources (some couple of hundred megabytes of RAM) to wing it.
        foreach (WAITING_AREAS as $pos) {
            $occ = $state['hallway'][$room_pos];
            // Spot occupied, cannot move there
            if ($occ !== '.') {
                continue;
            }
            $pos_from = min($pos, $room_pos);
            $path = substr($state['hallway'], $pos_from, abs($pos - $room_pos) + 1);

            // Path is not clear
            if (trim($path, '.') !== '') {
                continue;
            }

            $path .= str_repeat('.', ROOM_DEPTH - strlen(trim($state['rooms'][$room], '.')));
            $occupant = substr(trim($state['rooms'][$room], '.'), 0, 1);

            $new_state = $state;
            $new_state['rooms'][$room] = substr('.....' . substr(trim($state['rooms'][$room], '.'), 1), -ROOM_DEPTH);
            $new_state['hallway'] = substr($new_state['hallway'], 0, $pos) . $occupant . substr($new_state['hallway'], $pos + 1);

            $ret[] = ['state' => $new_state, 'cost' => strlen($path) * AMPHIPODS[$occupant]];
        }
    }
    return $ret;
}

// Dijkstra with [inverted] priority queue
function do_stuff($state)
{
    $queue = new \SplPriorityQueue();
    $queue->insert(['state' => $state, 'cost' => 0], 0);
    $visited = [];

    while (!$queue->isEmpty()) {
        [$state, $cost] = array_values($queue->extract());
        if (!isset($visited[state_to_string($state)])) {
            $visited[state_to_string($state)] = $state;
            if (is_win($state)) {
                dump_state($state);
                return $cost;
            }
            foreach (get_moves($state) as $move) {
                if (!isset($visited[state_to_string($move['state'])])) {
                    $queue->insert(['sate' => $move['state'], $cost => $cost + $move['cost']], -($cost + $move['cost']));
                }
            }
        }
    }

    // If we reach here, the bad news are "no solution found".
    return null;
}

echo "Result: " . do_stuff($state);