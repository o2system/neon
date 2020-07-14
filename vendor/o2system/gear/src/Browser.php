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

use O2System\Gear\Profiler\DataStructures\Metric;

/**
 * Class Browser
 *
 * @package O2System\Gear
 */
class Browser
{
    /**
     * Browser::$expression
     *
     * @var mixed
     */
    private $expression;

    // ------------------------------------------------------------------------

    /**
     * Browser::__construct
     *
     * @param mixed $expression
     */
    public function __construct($expression)
    {
        $this->expression = var_format($expression);
    }

    // ------------------------------------------------------------------------

    /**
     * Browser::render
     *
     * @return false|string
     */
    public function render()
    {
        $metric = new Metric('print-out');
        $metric->start();
        ini_set('memory_limit', '512M');

        if ( ! is_string($this->expression)) {
            $this->expression = print_r($this->expression, true);
        }

        $expression = htmlentities($this->expression);
        $expression = htmlspecialchars(htmlspecialchars_decode($this->expression, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
        $trace = new Trace();

        $metric->stop();

        ob_start();
        include __DIR__ . '/Views/Screen.php';
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}