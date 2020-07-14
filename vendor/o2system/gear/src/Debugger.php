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
 * O2System Gear Debug
 *
 * @package O2System\Gear
 */
class Debugger
{
    /**
     * Debugger::$chronology
     *
     * List of Debug Chronology
     *
     * @access  private
     * @static
     *
     * @type    array
     */
    private static $chronology = [];

    // ------------------------------------------------------------------------

    /**
     * Debugger::start
     *
     * Start Debug Process
     *
     * @access  public
     * @static  static method
     */
    public static function start()
    {
        static::$chronology = [];
        static::$chronology[] = static::whereCall(__CLASS__ . '::start()', 'debug_start()');
    }

    // ------------------------------------------------------------------------

    /**
     * Debugger::whereCall
     *
     * Where Call Method
     *
     * Finding where the call is made
     *
     * @access          private
     *
     * @param   $call   String Call Method
     *
     * @return          Trace Object
     */
    private static function whereCall($call, $helper)
    {
        $tracer = new Trace();

        foreach ($tracer->getChronology() as $trace) {
            if ($trace->call === $helper) {
                return $trace;
                break;
            } elseif ($trace->call === $call) {
                return $trace;
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Debugger::line
     *
     * Add debug line output
     *
     * @param mixed $expression
     * @param bool  $export
     */
    public static function line($expression, $export = false)
    {
        $trace = static::whereCall(__CLASS__ . '::line()', 'debug_line()');

        if ($export === true) {
            $trace->expression = var_export($expression, true);
        } else {
            $trace->expression = var_format($expression);
        }

        static::$chronology[] = $trace;
    }

    // ------------------------------------------------------------------------

    /**
     * Debugger::marker
     *
     * Set Debug Marker
     */
    public static function marker()
    {
        $trace = static::whereCall(__CLASS__ . '::marker()', 'debug_marker()');
        static::$chronology[] = $trace;
    }

    // ------------------------------------------------------------------------

    /**
     * Debugger::stop
     *
     * Stop Debug
     */
    public static function stop()
    {
        static::$chronology[] = static::whereCall(__CLASS__ . '::stop()', 'debug_stop()');
        static::render();
    }

    // ------------------------------------------------------------------------

    /**
     * Debugger::render
     */
    public static function render()
    {
        $trace = static::$chronology;

        ob_start();
        include __DIR__ . '/Views/Debugger.php';
        $output = ob_get_contents();
        ob_end_clean();

        static::$chronology = [];

        echo $output;
    }
}