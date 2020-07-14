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
 * O2System Gear Profiler
 *
 * @package O2System\Gear
 */
class Profiler
{
    /**
     * Profiler::$startTime
     *
     * Profiler Start Time
     *
     * @var float
     */
    private $startTime;

    /**
     * Profiler::$startMemory
     *
     * Profiler Start Memory Usage
     *
     * @var float
     */
    private $startMemory;

    /**
     * Profiler::$metrics
     *
     * Profiler Metrics Stack
     *
     * @var Profiler\Metrics
     */
    private $metrics;

    // ------------------------------------------------------------------------

    /**
     * Profiler::__construct
     *
     * @return Profiler
     */
    public function __construct()
    {
        $this->startTime = defined('STARTUP_TIME')
            ? STARTUP_TIME
            : microtime(true);

        $this->startMemory = defined('STARTUP_MEMORY')
            ? STARTUP_MEMORY
            : memory_get_usage(true);

        $this->metrics = new Profiler\Metrics();

        $this->watch('Starting Profiler Service');
    }

    // ------------------------------------------------------------------------

    /**
     * Profiler::watch
     *
     * @param string $marker
     */
    public function watch($marker)
    {
        // Stop Last Benchmark
        $this->metrics->push(new Profiler\DataStructures\Metric($marker));
    }

    // ------------------------------------------------------------------------

    /**
     * Profiler::setStartTime
     *
     * @param float $startTime
     *
     * @return Profiler
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Profiler::setStartMemory
     *
     * @param float $startMemory
     *
     * @return Profiler
     */
    public function setStartMemory($startMemory)
    {
        $this->startMemory = $startMemory;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Profiler::getMetrics
     *
     * @return \O2System\Gear\Profiler\Metrics
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    // ------------------------------------------------------------------------

    /**
     * Profiler::getTotalExecution
     *
     * @return mixed
     */
    public function getTotalExecution()
    {
        return $this->metrics->bottom();
    }
}