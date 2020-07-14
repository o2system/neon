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

namespace O2System\Gear\Profiler\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Metric
 *
 * @package O2System\Gear\Profiler\DataStructures
 */
class Metric
{
    /**
     * Metric::$marker
     *
     * @var string
     */
    public $marker;

    /**
     * Metric::$startTime
     *
     * @var int
     */
    public $startTime;

    /**
     * Metric::$endTime
     *
     * @var int
     */
    public $endTime;

    /**
     * Metric::$startMemory
     *
     * @var int
     */
    public $startMemory;

    /**
     * Metric::$endMemory
     *
     * @var int
     */
    public $endMemory;

    // ------------------------------------------------------------------------

    /**
     * Metric::__construct
     *
     * @param string $marker
     */
    public function __construct($marker)
    {
        $this->marker = $marker;

        $this->start();
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::start
     *
     * @param int|null $startTime
     * @param int|null $startMemory
     */
    public function start($startTime = null, $startMemory = null)
    {
        $this->startTime = isset($startTime) ? $startTime : microtime(true);
        $this->startMemory = isset($startMemory) ? $startMemory : memory_get_usage(true);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getStartTime
     *
     * @param int  $precision
     * @param int  $floatingPrecision
     * @param bool $showUnit
     *
     * @return float|string
     */
    public function getStartTime($precision = 0, $floatingPrecision = 3, $showUnit = true)
    {
        return $this->getFormattedTime($this->startTime, $precision, $floatingPrecision, $showUnit);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getFormattedTime
     *
     * @param int  $time
     * @param int  $precision
     * @param int  $floatingPrecision
     * @param bool $showUnit
     *
     * @return float|string
     */
    public function getFormattedTime(
        $time,
        $precision = 0,
        $floatingPrecision = 3,
        $showUnit = true
    ) {

        $test = is_int(
                $precision
            ) && $precision >= 0 && $precision <= 2 &&
            is_float($time) &&
            is_int($floatingPrecision) && $floatingPrecision >= 0 &&
            is_bool($showUnit);

        if ($test) {
            $duration = round($time * 10 * ($precision * 3), $floatingPrecision);

            if ($showUnit) {
                switch ($precision) {
                    case 0 :
                        return $duration . ' s';
                    case 1 :
                        return $duration . ' ms';
                    case 2 :
                        return $duration . ' µs';
                    default :
                        return $duration . ' (no unit)';
                }
            } else {
                return $duration;
            }
        } else {
            return 'Can\'t return the render time';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getEndTime
     *
     * @param int  $precision
     * @param int  $floatingPrecision
     * @param bool $showUnit
     *
     * @return float|string
     */
    public function getEndTime($precision = 0, $floatingPrecision = 3, $showUnit = true)
    {
        if (empty($this->endTime)) {
            $this->stop();
        }

        return $this->getFormattedTime($this->endTime, $precision, $floatingPrecision, $showUnit);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::stop
     *
     * @return static
     */
    public function stop()
    {
        $this->endTime = microtime(true);
        $this->endMemory = memory_get_peak_usage(true);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getDuration
     *
     * @param int  $precision
     * @param int  $floatingPrecision
     * @param bool $showUnit
     *
     * @return float|string
     */
    public function getDuration($precision = 0, $floatingPrecision = 3, $showUnit = true)
    {
        if (empty($this->endTime)) {
            $this->stop();
        }

        return $this->getFormattedTime($this->endTime - $this->startTime, $precision, $floatingPrecision, $showUnit);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getStartMemory
     *
     * @return string
     */
    public function getStartMemory()
    {
        return $this->getFormattedMemorySize($this->startMemory);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getFormattedMemorySize
     *
     * @param int $size
     *
     * @return string
     */
    public function getFormattedMemorySize($size)
    {
        if ($size < 1024) {
            return $size . " bytes";
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . " kb";
        } else {
            return round($size / 1048576, 2) . " mb";
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getMemoryUsage
     *
     * @return string
     */
    public function getMemoryUsage()
    {
        if (empty($this->endMemory)) {
            $this->stop();
        }

        return $this->getFormattedMemorySize($this->endMemory - $this->startMemory);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getMemory
     *
     * @return string
     */
    public function getMemory()
    {
        return $this->getEndMemory();
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getEndMemory
     *
     * @return string
     */
    public function getEndMemory()
    {
        return $this->getFormattedMemorySize($this->endMemory);
    }

    // ------------------------------------------------------------------------

    /**
     * Metric::getPeakMemoryUsage
     *
     * @return string
     */
    public function getPeakMemoryUsage()
    {
        return $this->getFormattedMemorySize(memory_get_peak_usage(true));
    }
}