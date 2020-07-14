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
 * Class Twig
 *
 * This class driver for Twig Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Template\Adapters
 */
class Twig extends AbstractAdapter
{
    /**
     * Twig::initialize
     *
     * @param array $config
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function initialize(array $config  =[])
    {
        if (empty($this->engine)) {
            if ($this->isSupported()) {
                $this->engine = new \Twig_Environment(new \Twig_Loader_String());
            } else {
                throw new RuntimeException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['Twig Template Engine by Sensio Labs', 'http://twig.sensiolabs.org/']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Twig::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\Twig_Environment')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Twig::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        return $this->engine->render($this->string, $vars);
    }

    // ------------------------------------------------------------------------

    /**
     * Twig::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \Twig_Environment) {
            return true;
        }

        return false;
    }
}