<?php

if (count($argv) !== 2) {
    die('File as param');
}

$lines = file($argv[1]);

$numbers = [];
$cards = [];
$cards_index = 0;
$line_index = 0;

foreach ($lines as $line) {
    $line = trim($line);
    if (!count($numbers)) {
        $numbers = array_map(fn ($val) => (int)$val, explode(',', $line));
    } else {
        if (!strlen($line)) {
            $line_index = 0;
            if (count($cards)) {
                $cards_index++;
            }
        } else {
            $cards[$cards_index][$line_index] = array_map(fn ($val) => (int)$val, preg_split('/\s+/', $line));
            $line_index++;
        }
    }
}

$orig_cards = $cards;

function validateVictory($cards)
{
    foreach ($cards as $card_index => $card) {
        foreach ($card as $lines) {
            if (strlen(trim(implode('', $lines), 'x')) === 0) {
                return $card_index;
            }
        }
        foreach ($card[0] as $k => $_) {
            if (strlen(trim(implode('', array_map(fn ($line) => $line[$k], $card)), 'x')) === 0) {
                return $card_index;
            }
        }
    }

    return false;
}

$winner_card = false;
foreach ($numbers as $round => $number) {
    foreach ($cards as $card_index => $card) {
        foreach ($card as $row_index => $row) {
            foreach ($row as $col_index => $col) {
                if ($col === $number) {
                    $cards[$card_index][$row_index][$col_index] = "x";
                }
            }
        }
    }

    $winner_card = validateVictory($cards);

    while ($winner_card !== false){
        $called = $number;
        $winner = $cards[$winner_card];
        unset($cards[$winner_card]);
        $winner_card = validateVictory($cards);
    }
}

$sum = 0;
foreach ($winner as $line) {
    $sum += array_sum($line);
}

echo "Sum: $sum * $called = " . ($sum * $called) . "\n";
