<?php

if (count($argv) !== 2) {
    die('Usage: ' . basename(__FILE__) . " [file|message]\n");
}

# If a file, read it. If string, that's it.
if (file_exists($argv[1])) {
    $msg = trim(file_get_contents($argv[1]));
} else {
    $msg = $argv[1];
}

# If hex, parse it. If bin, that's it.
if (strlen(str_replace(['0', '1'], '', $msg))) {
    $msg = implode('', array_map(fn($char) => substr('000' . decbin(hexdec($char)), -4), str_split($msg)));
}

function decode(string $bits, $packet_limit = 0, $pos_limit = 0): array
{
    $versions = [];
    $packets_decoded = 0;
    $pos = 0;
    $ret = [];
    while ($pos + 6 < strlen($bits)) {
        $versions[] = bindec(substr($bits, $pos, 3));
        $type_id = bindec(substr($bits, $pos + 3, 3));
        $pos += 6;
        switch ($type_id) {
            case 4:
                $value = '';
                while (true) {
                    $part = substr($bits, $pos, 5);
                    $pos += 5;
                    $value .= substr($part, 1);
                    if ($part[0] === '0') {
                        break;
                    }
                }
                $ret[] = bindec($value);
                break;
            default:
                $length_type_id = $bits[$pos];
                $pos += 1;

                $length = $number = 0;
                if ($length_type_id == '0') {
                    $length = bindec(substr($bits, $pos, 15));
                    $pos += 15;
                } else {
                    $number = bindec(substr($bits, $pos, 11));
                    $pos += 11;
                }
                [$values, $next_pos, $next_versions] = decode(substr($bits, $pos), $number, $length);
                $pos += $next_pos;
                $versions = array_merge($versions, $next_versions);

                $operator = ['sum', 'mul', 'min', 'max', null, 'gt', 'lt', 'eq'][$type_id];

                if (count($values)) {
                    $ret[] = match ($operator) {
                        'sum' => array_sum($values),
                        'mul' => array_reduce($values, fn($a, $b) => $a * $b, 1),
                        'min' => min($values),
                        'max' => max($values),
                        'gt' => (int)($values[0] > $values[1]),
                        'lt' => (int)($values[0] < $values[1]),
                        'eq' => (int)($values[0] == $values[1]),
                    };
                }
        }
        $packets_decoded++;
        if ($pos_limit && $pos >= $pos_limit) {
            break;
        }
        if ($packet_limit && $packets_decoded >= $packet_limit) {
            break;
        }
    }
    return [$ret, $pos, $versions];
}

$result = decode($msg);

$result1 = array_sum($result[2]);
$result2 = $result[0][0];

echo "Result1: $result1\n";
echo "Result2: " . $result2 . "\n";