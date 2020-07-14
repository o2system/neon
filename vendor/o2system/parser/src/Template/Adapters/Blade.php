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
 * Class Blade
 *
 * This class driver for Laravel's Blade Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Template\Adapters
 */
class Blade extends AbstractAdapter
{
    /**
     * Blade::initialize
     *
     * @param array $config
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function initialize(array $config = [])
    {
        $config = array_merge($this->config, $config);

        if (empty($this->engine)) {
            if ($this->isSupported()) {
                $this->engine = new \O2System\Parser\Template\Engines\Blade($config);
            } else {
                throw new RuntimeException('PARSER_E_THIRD_PARTY', 0, ['\O2System\Parser\Engines\Blade']);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\O2System\Parser\Template\Engines\Blade')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::parse
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
     * Blade::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \O2System\Parser\Template\Engines\Blade) {
            return true;
        }

        return false;
    }
}