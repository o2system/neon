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

namespace O2System\Parser\Template\Adapters;

// ------------------------------------------------------------------------

use O2System\Parser\Template\Abstracts\AbstractAdapter;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Smarty
 *
 * This class driver for Smarty Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Template\Adapters
 */
class Smarty extends AbstractAdapter
{
    /**
     * Smarty::initialize
     *
     * @param array $config
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function initialize(array $config = [])
    {
        if (empty($this->engine)) {
            if ($this->isSupported()) {
                $this->engine = new \Smarty();
            } else {
                throw new RuntimeException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['Smarty Template Engine by New Digital Group, Inc', 'http://www.smarty.net/']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Smarty::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\Smarty')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Smarty::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        foreach ($vars as $_assign_key => $_assign_value) {
            $this->engine->assign($_assign_key, $_assign_value);
        }

        return $this->engine->fetch('string:' . $this->string);
    }

    // ------------------------------------------------------------------------

    /**
     * Smarty::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \Smarty) {
            return true;
        }

        return false;
    }
}