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

namespace O2System\Parser\String\Adapters;

// ------------------------------------------------------------------------

use O2System\Parser\String\Abstracts\AbstractAdapter;

/**
 * Class Shortcodes
 *
 * @package O2System\Parser\Drivers
 */
class Shortcodes extends AbstractAdapter
{
    /**
     * Shortcodes::initialize
     *
     * @param array $config
     *
     * @return static
     */
    public function initialize(array $config = [])
    {
        $config = array_merge($this->config, $config);

        if (empty($this->engine)) {
            $this->engine = new \O2System\Parser\String\Engines\Shortcodes($config);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        return $this->engine->parseString($this->string, $vars);
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\O2System\Parser\String\Engines\Shortcodes')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \O2System\Parser\String\Engines\Shortcodes) {
            return true;
        }

        return false;
    }
}