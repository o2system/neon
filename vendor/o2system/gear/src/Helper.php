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
/**
 * Printer Helper
 *
 * A collection of helper function to help PHP programmer to prints a human-readable information
 * about a variable, object and etc into the browser or command line.
 */

// ------------------------------------------------------------------------

if ( ! function_exists('var_format')) {
    /**
     * var_format
     *
     * Formats a variable with extra information.
     *
     * @param mixed $expression The variable tobe formatted.
     *
     * @return mixed
     */
    function var_format($expression)
    {
        if (is_bool($expression)) {
            if ($expression === true) {
                $expression = '(bool) TRUE';
            } else {
                $expression = '(bool) FALSE';
            }
        } elseif (is_resource($expression)) {
            $expression = '(resource) ' . get_resource_type($expression);
        } elseif (is_array($expression) || is_object($expression)) {
            $expression = @print_r($expression, true);
        } elseif (is_int($expression) OR is_numeric($expression)) {
            $expression = '(int) ' . $expression;
        } elseif (is_null($expression)) {
            $expression = '(null)';
        }

        return $expression;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_out')) {
    /**
     * print_code
     *
     * Prints a variable into Gear Browser.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_out($expression, $exit = true)
    {
        if (php_sapi_name() === 'cli') {
            print_cli($expression, $exit);

            return;
        } elseif ( ! empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower(
                $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]
            ) === 'xmlhttprequest') {
            if (is_array($expression)) {
                echo json_encode($expression, JSON_PRETTY_PRINT);
            } elseif (is_object($expression)) {
                print_r($expression);
            } else {
                echo $expression;
            }

            if ($exit) {
                die;
            }
        } else {
            echo (new \O2System\Gear\Browser($expression))->render();

            if ($exit) {
                die;
            }
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_console')) {
    /**
     * print_console
     *
     * Prints a variable into browser console.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_console(
        $expression,
        $label = null,
        $messageType = \O2System\Gear\Console::LOG_MESSAGE,
        $exit = true
    ) {
        if (php_sapi_name() === 'cli') {
            print_cli($expression, $exit);

            return;
        }

        (new \O2System\Gear\Console($label, $expression, $messageType))->send();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_cli')) {
    /**
     * print_cli
     *
     * Prints a variable into command line interface output.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_cli($expression, $exit = true)
    {
        (new \O2System\Gear\Cli($expression))->send();

        if ($exit) {
            die;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_code')) {
    /**
     * print_code
     *
     * Prints a variable inside pre-code html tag.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_code($expression, $exit = false)
    {
        $expression = htmlentities(var_format($expression));
        $expression = htmlspecialchars(htmlspecialchars_decode($expression, ENT_QUOTES), ENT_QUOTES, 'UTF-8');

        echo '<pre>' . $expression . '</pre>';

        if ($exit === true) {
            die;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_dump')) {
    /**
     * print_dump
     *
     * Prints a dumps information about a variable inside pre-code html tag.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_dump($expression, $exit = false)
    {
        ob_start();
        var_dump($expression);
        $output = ob_get_contents();
        ob_end_clean();

        if (strpos($output, 'xdebug-var-dump') !== false) {
            if (defined('PATH_ROOT')) {
                $helper_file = implode(DIRECTORY_SEPARATOR,
                    ['vendor', 'o2system', 'gear', 'src', 'Helper.php:170:']);
                $output = str_replace('<small>' . PATH_ROOT . $helper_file . '</small>', '', $output);
            }

            echo $output;

            if ($exit) {
                die;
            }
        } else {
            print_code($output, $exit);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_json')) {
    /**
     * print_json
     *
     * Prints a variable into json formatted string.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param int   $options    The optional json_encode options.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_json($expression, $options = null, $exit = true)
    {
        if (is_bool($options)) {
            $exit = $options;
            $options = null;
        }

        if (is_array($expression) || is_object($expression)) {
            if (empty($options)) {
                $output = json_encode($expression);
            } else {
                $output = json_encode($expression, $options);
            }

            print_out($output, $exit);
        } else {
            print_out('Invalid Expression!', $exit);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_serialize')) {
    /**
     * print_serialize
     *
     * Prints a variable into serialized formatted string.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_serialize($expression, $exit = true)
    {
        if (is_array($expression) || is_object($expression)) {
            print_out(serialize($expression), $exit);
        } else {
            print_out('Invalid Expression!', $exit);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('print_line')) {
    /**
     * print_line
     *
     * Prints a multiple stacked lines of variable.
     *
     * The print_line command can be placed in various places in the source code program,
     * the print_line will stacked all variable into a static memory and will be printed when
     * the expression is fill with --- string.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $exit       The exit flag of the current script execution.
     */
    function print_line($expression, $exit = false)
    {
        static $lines;

        if (strtoupper($exit) === 'FLUSH') {
            $lines = [];
            $lines[] = $expression;
        }

        if (is_array($expression) || is_object($expression)) {
            $lines[] = print_r($expression, true);
        } else {
            $lines[] = var_format($expression);
        }

        if ($exit === true || $expression === '---') {
            $expression = implode(PHP_EOL, $lines);
            $lines = [];

            print_out($expression, $exit);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('pre_open')) {
    /**
     * pre_open
     *
     * Prints an open pre HTML tag.
     */
    function pre_open()
    {
        echo '<pre>';
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('pre_line')) {
    /**
     * pre_line
     *
     * Prints a variable into pre HTML tag.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $implode    The flag to implode lines.
     */
    function pre_line($expression, $implode = true)
    {
        if (is_array($expression) AND $implode === true) {
            $expression = implode(PHP_EOL, $expression);
        } elseif (is_bool($expression)) {
            if ($expression === true) {
                $expression = '(bool) TRUE';
            } else {
                $expression = '(bool) FALSE';
            }
        } elseif (is_resource($expression)) {
            $expression = '(resource) ' . get_resource_type($expression);
        } elseif (is_array($expression) || is_object($expression)) {
            $expression = @print_r($expression, true);
        } elseif (is_int($expression) OR is_numeric($expression)) {
            $expression = '(int) ' . $expression;
        } elseif (is_null($expression)) {
            $expression = '(null)';
        }

        echo $expression . PHP_EOL;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('pre_close')) {
    /**
     * pre_close
     *
     * Prints a close pre HTML tag.
     */
    function pre_close($exit = false)
    {
        echo '</pre>';

        if ($exit) {
            die;
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('debug_start')) {
    /**
     * debug_start
     *
     * Starts a debugger stacks.
     */
    function debug_start()
    {
        \O2System\Gear\Debugger::start();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('debug_line')) {
    /**
     * debug_line
     *
     * Add a debug line variable into debugger stacks.
     *
     * @param mixed $expression The variable tobe formatted.
     * @param bool  $export     The variable export flag, to export the variable into parsable string representation.
     */
    function debug_line($expression, $export = false)
    {
        \O2System\Gear\Debugger::line($expression, $export);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('debug_marker')) {
    /**
     * debug_marker
     *
     * Add a debug marker into debugger stacks.
     */
    function debug_marker()
    {
        \O2System\Gear\Debugger::marker();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('debug_stop')) {
    /**
     * debug_stop
     *
     * Stop the script execution and prints the debugger output into browser.
     */
    function debug_stop()
    {
        \O2System\Gear\Debugger::stop();
        die;
    }
}