<?php
/** @noinspection AutoloadingIssuesInspection */

if (count($argv) < 2) {
    die('Usage: ' . basename(__FILE__) . " [file|line] [iterations?]\n");
}

if (file_exists($argv[1])) {
    $lines = array_filter(array_map(trim(...), file($argv[1])), trim(...));
} else {
    $lines = [$argv[1]];
}

define('DEBUG', $argv[2] ?? '' === 'DEBUG');

function debug($str)
{
    if (DEBUG) {
        echo "$str\n";
    }
}

class Number
{
    public Number|int $left;
    public Number|int $right;
    public ?Number $parent;

    public function __construct(array $arr, $parent = null)
    {
        $this->left = is_int($arr[0]) ? $arr[0] : new Number($arr[0], $this);
        $this->right = is_int($arr[1]) ? $arr[1] : new Number($arr[1], $this);
        $this->parent = $parent;
    }

    public function getLevel(): int
    {
        $current = $this;
        $level = 0;
        while ($current->parent !== null) {
            ++$level;
            $current = $current->parent;
        }
        return $level;
    }

    public function __toString(): string
    {
        $left = $this->left;
        $right = $this->right;
        $ret = "[";
        if (is_int($left) && $left > 9) {
            $ret .= "\033[34m$left\033[0m";
        } else {
            $ret .= $left;
        }
        $ret .= ',';
        if (is_int($right) && $right > 9) {
            $ret .= "\033[34m$right\033[0m";
        } else {
            $ret .= $right;
        }
        $ret .= "]";
        if ($this->getLevel() > 3) {
            return "\033[31m$ret\033[0m";
        }
        return $ret;
    }

    public function explode(int $level = 0): bool
    {
        $exploded = false;

        if (!is_int($this->left)) {
            $exploded = $this->left->explode($level + 1);
        }
        if (!$exploded && !is_int($this->right)) {
            $exploded = $this->right->explode($level + 1);
        }
        if ($level > 3 && is_int($this->left) && is_int($this->right)) {
            $current = $this;
            while ($current->parent !== null) {
                if (is_int($current->parent->right)) {
                    $current->parent->right += $this->right;
                    break;
                }

                if ($current->parent->right !== $current) {
                    $current = $current->parent->right;
                    while (!is_int($current->left)) {
                        $current = $current->left;
                    }
                    $current->left += $this->right;
                    break;
                }

                $current = $current->parent;
            }

            $current = $this;
            while ($current->parent !== null) {
                if (is_int($current->parent->left)) {
                    $current->parent->left += $this->left;
                    break;
                }

                if ($current->parent->left !== $current) {
                    $current = $current->parent->left;
                    while (!is_int($current->right)) {
                        $current = $current->right;
                    }
                    $current->right += $this->left;
                    break;
                }

                $current = $current->parent;
            }

            if ($this->parent->left === $this) {
                $this->parent->left = 0;
            } else {
                $this->parent->right = 0;
            }
            return true;
        }
        return $exploded;
    }

    public function split(): bool
    {
        foreach (['left', 'right'] as $leaf) {
            if ($this->$leaf instanceof self) {
                if ($this->$leaf->split()) {
                    return true;
                }
            } elseif (is_int($this->$leaf) && $this->$leaf > 9) {
                $this->$leaf = new Number([(int)floor($this->$leaf / 2), (int)ceil($this->$leaf / 2)], $this);
                return true;
            }
        }

        return false;
    }

    public function reduce(): void
    {
        $old = '';
        while ($old !== (string)$this) {
            $old = (string)$this;
            if ($this->explode()) {
                debug("After explode: $this");
            } else if ($this->split()) {
                debug("After split:   $this");
            }
        }
    }

    public function magnitude(): int
    {
        $left = is_int($this->left) ? $this->left : $this->left->magnitude();
        $right = is_int($this->right) ? $this->right : $this->right->magnitude();

        return $left * 3 + $right * 2;
    }
}

/** @noinspection PhpUnhandledExceptionInspection */
$number = new Number(json_decode($lines[0], false, 512, JSON_THROW_ON_ERROR));
for ($i = 1, $len = count($lines); $i < $len; $i++) {
    debug("  $number");
    debug("+ " . $lines[$i]);

    $line = $lines[$i];
    /** @noinspection PhpUnhandledExceptionInspection */
    $number = new Number(json_decode("[$number,$line]", false, 512, JSON_THROW_ON_ERROR));
    debug("= $number");
    $number->reduce();
    debug("=>" . $number);
}

echo 'Part 1: ' . $number->magnitude() . "\n";

$cnt = count($lines);
$result2 = 0;
$magnitudes = [];
for ($i = 0; $i < $cnt; $i++) {
    for ($j = 0; $j < $cnt; $j++) {
        if ($i === $j) {
            continue;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = (new Number(json_decode("[$lines[$i],$lines[$j]]", false, 512, JSON_THROW_ON_ERROR)));
        $result->reduce();
        $magnitude = $result->magnitude();
        $magnitudes[$magnitude] = [(string)$lines[$i], (string)($lines[$j]), (string)$result];

        $result2 = max($magnitude, $result2);
    }
}

echo 'Part 2: ' . $result2 . "\n";
