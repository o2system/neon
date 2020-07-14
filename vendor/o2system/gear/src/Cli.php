<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Gear;

// ------------------------------------------------------------------------

/**
 * Class Cli
 *
 * @package O2System\Gear
 */
class Cli
{
    /**
     * Cli::$expression
     *
     * @var mixed
     */
    private $expression;

    // ------------------------------------------------------------------------

    /**
     * Cli::__construct
     *
     * @param mixed $expression
     */
    public function __construct($expression)
    {
        $this->expression = var_format($expression);
    }

    // ------------------------------------------------------------------------

    /**
     * Cli::send
     */
    public function send()
    {
        $trace = new \O2System\Gear\Trace();

        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
        echo PHP_EOL . 'START of gears:print_cli' . PHP_EOL;
        echo "--------------------------------------------------------------------------------------" . PHP_EOL . PHP_EOL;
        if ( ! is_string($this->expression)) {
            $this->expression = print_r($this->expression, true);
        }
        echo $this->expression . PHP_EOL;
        echo PHP_EOL . '--------------------------------------------------------------------------------------' . PHP_EOL . PHP_EOL;

        echo 'DEBUG BACKTRACE' . PHP_EOL;
        echo '--------------------------------------------------------------------------------------' . PHP_EOL . PHP_EOL;
        $i = 1;
        foreach ($trace->getChronology() as $chronology) {
            echo $i . '. Method: ' . $chronology->call . PHP_EOL;
            echo str_repeat(
                    ' ',
                    strlen($i)
                ) . '  Line: ' . $chronology->file . ':' . $chronology->line . PHP_EOL . PHP_EOL;
            $i++;

            if ($chronology->call === 'print_cli()') {
                break;
            }
        }

        echo PHP_EOL . "-------------------------------------------------------------------------------------- " . PHP_EOL;
        echo 'END of gears:print_cli' . PHP_EOL . PHP_EOL;
    }
}