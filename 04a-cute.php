<?php

if (count($argv) !== 2) {
    die('File as param');
}

class Game
{
    /**
     * @var array<int>
     */
    private array $numbers = [];

    private int $current_number;

    /**
     * @var array<array<int>>
     */
    private array $boards = [];

    public function __construct(string $fname)
    {
        $this->readFile($fname);
    }

    /**
     * Outputs current game state.
     */
    public function dumpCards(): void
    {
        $lines = count($this->boards[0]);
        $called = isset($this->current_number) ? (string)($this->current_number) : 'Initial state';
        echo '+' . str_repeat('-', count($this->boards[0][0]) * 3 * count($this->boards) + 2 * count($this->boards) - 1) . "+\n";
        echo '| ' . $called;
        echo str_repeat(' ', count($this->boards[0][0]) * 3 * count($this->boards) + 2 * count($this->boards) - 1 - strlen($called)-1) . "|\n";
        echo '+' . str_repeat('-', count($this->boards[0][0]) * 3 * count($this->boards) + 2 * count($this->boards) - 1) . "+\n";
        for ($line = 0; $line < $lines; $line++) {
            echo '| ';
            foreach ($this->boards as $board) {
                echo implode(' ', array_map(fn($number) => str_pad($number, 2, ' ', STR_PAD_LEFT), $board[$line]));
                echo " | ";
            }
            echo "\n";
        }
        echo str_repeat('+-' . str_repeat('-', count($this->boards[0][0]) * 3), count($this->boards)) . "+\n";
    }

    /**
     * Calls a number to play.
     */
    public function call(): void
    {
        $this->current_number = array_shift($this->numbers);
        foreach ($this->boards as $board_index => $board) {
            foreach ($board as $line_index => $line) {
                foreach ($line as $num_index => $num) {
                    if ($num === $this->current_number) {
                        $this->boards[$board_index][$line_index][$num_index] = 'x';
                    }
                }
            }
        }
    }

    /**
     * Reads file and sets up state.
     */
    private function readFile(string $fname): void
    {
        /**
         * Example file. Called numbers on the first line, boards follor.
         *
         * 7,4,9,5,11,17,23,2,0,14,21,24,10,16,13,6,15,25,12,22,18,20,8,19,3,26,1
         *
         * 22 13 17 11  0
         * 8  2 23  4 24
         * 21  9 14 16  7
         * 6 10  3 18  5
         * 1 12 20 15 19
         *
         * 3 15  0  2 22
         * 9 18 13 17  5
         * 19  8  7 25 23
         * 20 11 10 24  4
         * 14 21 16 12  6
         */
        $lines = file($fname);
        $this->numbers = array_map(static fn($number) => (int)$number, explode(',', $lines[0]));

        $boards = explode("\n\n", implode(array_slice($lines, 1)));
        foreach ($boards as $board_index => $board) {
//            echo $board;
            foreach (explode("\n", trim($board)) as $line) {
                $this->boards[$board_index][] = array_map(fn($number) => (int)$number, preg_split('/\s+/', trim($line)));
            }
        }
    }
}

$game = new Game($argv[1]);
$game->dumpCards();
$game->call();
$game->dumpCards();
