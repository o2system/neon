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
 * O2System Gear Trace
 *
 * @package O2System\Gear
 */
class Trace
{
    /**
     * Trace::$backtrace
     *
     * @type    string name of called class
     */
    protected $backtrace = null;

    /**
     * Trace::$chronology
     *
     * @var array
     */
    protected $chronology = [];

    // ------------------------------------------------------------------------

    /**
     * Trace::__construct
     *
     * @param array $trace
     */
    public function __construct($trace = [])
    {
        if ( ! empty($trace)) {
            $this->backtrace = $trace;
        } else {
            $this->backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        // reverse array to make steps line up chronologically
        $this->backtrace = array_reverse($this->backtrace);

        // Generate Lines
        $this->setChronology();
    }

    // ------------------------------------------------------------------------

    /**
     * Trace::setChronology
     *
     * Generate Chronology Method
     *
     * Generate array of Backtrace Chronology
     *
     * @access           private
     * @return           void
     */
    private function setChronology()
    {
        foreach ($this->backtrace as $trace) {
            $line = new Trace\DataStructures\Chronology($trace);

            if (isset($trace[ 'class' ]) AND isset($trace[ 'type' ])) {
                $line->call = $trace[ 'class' ] . $trace[ 'type' ] . $trace[ 'function' ] . '()';
                $line->type = $trace[ 'type' ] === '->' ? 'non-static' : 'static';
            } else {
                $line->call = $trace[ 'function' ] . '()';
                $line->type = 'non-static';
            }

            if ( ! isset($trace[ 'file' ])) {
                $currentTrace = current($this->backtrace);
                $line->file = isset($currentTrace[ 'file' ]) ? $currentTrace[ 'file' ] : null;
                $line->line = isset($currentTrace[ 'line' ]) ? $currentTrace[ 'line' ] : null;
            }

            if (defined('PATH_ROOT')) {
                $line->file = str_replace(PATH_ROOT, '', $line->file);
            }

            $this->chronology[] = $line;

            if (in_array($line->call, ['print_out()', 'print_line()', 'O2System\Core\Gear\Debug::stop()'])) {
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Trace::getChronology
     *
     * @param bool $reset
     *
     * @return array
     */
    public function getChronology($reset = true)
    {
        $chronology = $this->chronology;

        if ($reset === true) {
            $this->chronology = [];
        }

        return $chronology;
    }
}