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

use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class UnitTesting
 *
 * @package O2System\Gear
 */
class UnitTesting
{
    /**
     * UnitTesting::$reports
     *
     * @var \O2System\Spl\Iterators\ArrayIterator
     */
    private $reports;

    // ------------------------------------------------------------------------

    /**
     * UnitTesting::__construct
     */
    public function __construct()
    {
        $this->reports = new ArrayIterator();
    }

    // ------------------------------------------------------------------------

    /**
     * UnitTesting::test
     *
     * @param string       $label
     * @param \Closure     $closure
     * @param mixed        $expected
     * @param string|null  $notes
     */
    public function test($label, $closure, $expected, $notes = null)
    {
        if ($closure instanceof \Closure) {
            $closure = call_user_func($closure, $this);
        }

        if (in_array($expected, [
            'object',
            'string',
            'bool',
            'true',
            'false',
            'int',
            'integer',
            'numeric',
            'float',
            'double',
            'array',
            'null',
            'resource',
        ], true)) {
            $expectedDataType = $expected;
            $passed = (bool)call_user_func('is_' . $expected, $closure);
        } else {
            $expectedDataType = gettype($expected);
            $passed = (bool)($closure === $expected);
        }

        if (is_bool($closure)) {
            $closure = ($closure === true) ? 'true' : 'false';
        }

        if (is_bool($expected)) {
            $expected = ($expected === true) ? 'true' : 'false';
        }

        $this->reports[] = new \ArrayObject([
            'label'    => $label,
            'result'   => $closure,
            'expected' => $expected,
            'datatype' => new \ArrayObject([
                'expected' => $expectedDataType,
                'result'   => gettype($closure),
            ], \ArrayObject::ARRAY_AS_PROPS),
            'status'   => ($passed === true) ? 'passed' : 'failed',
            'trace'    => $this->getBacktrace(),
            'notes'    => $notes,
        ], \ArrayObject::ARRAY_AS_PROPS);
    }

    // ------------------------------------------------------------------------

    /**
     * UnitTesting::getBacktrace
     *
     * @return \O2System\Gear\Trace\DataStructures\Chronology
     */
    protected function getBacktrace()
    {
        $backtrace = debug_backtrace();
        $backtrace = $backtrace[ 1 ];

        if (isset($backtrace[ 'class' ]) AND isset($backtrace[ 'type' ])) {
            $chronology[ 'call' ] = $backtrace[ 'class' ] . $backtrace[ 'type' ] . $backtrace[ 'function' ] . '()';
            $chronology[ 'type' ] = $backtrace[ 'type' ] === '->' ? 'non-static' : 'static';
        } else {
            $chronology[ 'call' ] = $backtrace[ 'function' ] . '()';
            $chronology[ 'type' ] = 'non-static';
        }

        if (isset($backtrace[ 'file' ])) {
            $chronology[ 'file' ] = (isset($backtrace[ 'file' ]) ? $backtrace[ 'file' ] : '');
            $chronology[ 'line' ] = (isset($backtrace[ 'line' ]) ? $backtrace[ 'line' ] : '');
        }

        if (defined('PATH_ROOT')) {
            $chronology[ 'file' ] = str_replace(PATH_ROOT, '', $chronology[ 'file' ]);
        }

        return new Trace\DataStructures\Chronology($chronology);
    }

    // ------------------------------------------------------------------------

    /**
     * UnitTesting::getReports
     *
     * @return \O2System\Spl\Iterators\ArrayIterator
     */
    public function getReports()
    {
        return $this->reports;
    }

    // ------------------------------------------------------------------------

    /**
     * UnitTesting::render
     */
    public function render()
    {
        $reports = $this->reports;

        ob_start();
        include __DIR__ . '/Views/UnitTesting.php';
        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
    }
}