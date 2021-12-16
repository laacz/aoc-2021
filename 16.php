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

$result1 = 0;
$pos = 0;

function decode(string $bits, $packet_limit = 0, $pos_limit = 0, $level = 0)
{
    global $result1, $pos;
    $prefix = str_repeat('  ', $level);
    $packets_decoded = 0;
    $ret = [];
    while ($pos < strlen($bits) && ($pos + 6 < strlen($bits))) {
        $version = bindec(substr($bits, $pos, 3));
        $result1 += $version;
        $pos += 3;
        $type_id = bindec(substr($bits, $pos, 3));
        $pos += 3;
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
                echo "{$prefix}v$version Value $value => " . bindec($value) . "\n";
                $ret[] = bindec($value);
                break;
            default:
                $length_type_id = $bits[$pos];
                $pos += 1;
                $operator = ['sum', 'mul', 'min', 'max', null, 'gt', 'lt', 'eq'][$type_id];
                switch ($length_type_id) {
                    case '0':
                        $length = bindec(substr($bits, $pos, 15));
                        $pos += 15;
                        echo "{$prefix}v$version operator $operator with sub-packets length of $length bit(s)\n";
                        $values = decode($bits, 0, $pos + $length, $level + 1);
                        break;
                    case '1':
                        $number = bindec(substr($bits, $pos, 11));
                        $pos += 11;
                        echo "{$prefix}v$version operator $operator with $number packet(s)\n";
                        $values = decode($bits, $number, 0, $level + 1);
                        break;
                }
                echo "$prefix  $operator(" . implode(', ', $values) . ")\n";
                switch ($operator) {
                    case 'sum':
                        $ret[] = array_sum($values);
                        break;
                    case 'mul':
                        $ret[] = array_reduce($values, fn($a, $b) => $a * $b, 1);
                        break;
                    case 'min':
                        $ret[] = min($values);
                        break;
                    case 'max':
                        $ret[] = max($values);
                        break;
                    case 'gt':
                        $ret[] = (int)($values[0] > $values[1]);
                        break;
                    case 'lt':
                        $ret[] = (int)($values[0] < $values[1]);
                        break;
                    case 'eq':
                        $ret[] = (int)($values[0] == $values[1]);
                        break;
                    default:
                        die("Unknown operator with type_id $type_id");
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
    return $ret;
}

$result2 = decode($msg);

echo "Result1: $result1\n";
echo "Result2: " . array_shift($result2) . "\n";