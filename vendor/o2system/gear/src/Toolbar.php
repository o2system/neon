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
 * Class Toolbar
 *
 * @package O2System\Gear
 */
class Toolbar
{
    /**
     * Toolbar::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getOutput();
    }

    // ------------------------------------------------------------------------

    /**
     * Toolbar::getOutput
     *
     * @return string
     */
    public function getOutput()
    {
        $totalExecution = profiler()->getTotalExecution();
        $metrics = profiler()->getMetrics();

        $totalTime = 0;
        $totalMemory = 0;

        foreach ($metrics as $metric) {
            $totalTime += ($metric->endTime - $metric->startTime);
            $totalMemory += ($metric->endMemory - $metric->startMemory);
        }

        $segmentDuration = $this->roundTo($totalTime * 1000, 5);
        $segmentCount = (int)ceil($totalTime / $segmentDuration);

        $displayTime = $segmentCount * $segmentDuration;

        foreach ($metrics as $metric) {
            $metric->offset = (($metric->startTime - $totalExecution->startTime) * 1000 / $displayTime) * 100;
            $metric->length = (($metric->endTime - $metric->startTime) * 1000 / $displayTime) * 100;
        }

        $totalTime = $totalExecution->getFormattedTime($totalTime, 2);
        $totalMemory = $totalExecution->getFormattedMemorySize($totalMemory);
        $allocatedMemory = $totalExecution->getFormattedMemorySize(memory_get_usage(true));
        $peakMemory = $totalExecution->getFormattedMemorySize(memory_get_peak_usage(true));

        $files = $this->getFiles();
        $logs = $this->getLogs();
        $vars = $this->getVars();
        $database = $this->getDatabase();

        ob_start();
        include __DIR__ . '/Views/Toolbar.php';
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    // ------------------------------------------------------------------------

    /**
     * Toolbar::roundTo
     *
     * Rounds a number to the nearest incremental value.
     *
     * @param float $number
     * @param int   $increments
     *
     * @return float
     */
    protected function roundTo($number, $increments = 5)
    {
        $increments = 1 / $increments;

        return (ceil($number * $increments) / $increments);
    }
    //--------------------------------------------------------------------

    /**
     * Toolbar::getFiles
     *
     * @return array
     */
    public function getFiles()
    {
        $files = get_included_files();

        if (class_exists('\O2System\Framework', false)) {
            foreach ($files as $key => $file) {

                if (strpos($file, 'autoload.php') !== false) {
                    unset($files[ $key ]);
                    continue;
                }

                $files[ $key ] = str_replace(PATH_ROOT, DIRECTORY_SEPARATOR, $file);
            }
        }

        return $files;
    }

    // ------------------------------------------------------------------------

    /**
     * Toolbar::getLogs
     *
     * @return array
     */
    public function getLogs()
    {
        $logs = [];

        if (function_exists('logger')) {
            $logs = logger()->getLines();
        }

        return $logs;
    }

    // ------------------------------------------------------------------------

    /**
     * Toolbar::getVars
     *
     * @return \ArrayObject
     */
    public function getVars()
    {
        $vars = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);

        $vars->env = $_ENV;
        $vars->server = $_SERVER;
        $vars->session = $_SESSION;
        $vars->cookies = $_COOKIE;
        $vars->get = $_GET;
        $vars->post = $_POST;
        $vars->files = $_FILES;

        if (function_exists('apache_request_headers')) {
            $vars->headers = apache_request_headers();
        } elseif (function_exists('getallheaders')) {
            $vars->headers = getallheaders();
        } else {
            $vars->headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $vars->headers[ str_replace(' ', '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))) ] = $value;
                }
            }
        }

        return $vars;
    }

    // ------------------------------------------------------------------------

    /**
     * Toolbar::getDatabase
     *
     * @return array
     */
    public function getDatabase()
    {
        $database = [];

        if (class_exists('O2System\Framework', false)) {
            if (function_exists('database')) {
                if(database() instanceof \O2System\Database\Connections) {
                    $connections = database()->getIterator();

                    foreach ($connections as $offset => $connection) {
                        $database[ $offset ] = $connection->getQueries();
                    }
                }
            }
        }

        return $database;
    }
}