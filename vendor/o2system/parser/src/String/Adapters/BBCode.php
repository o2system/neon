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
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class BBCode
 *
 * This class driver for Parse BBCode for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class BBCode extends AbstractAdapter
{
    /**
     * BBCode::initialize
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
                $this->engine = new \JBBCode\Parser();
                $this->engine->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
            } else {
                throw new RuntimeException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['BBCode Parser by Jackson Owens', 'https://github.com/jbowens/jBBCode']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BBCode::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\JBBCode\Parser')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * BBCode::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        $this->engine->parse($this->string);

        return $this->engine->getAsHtml();
    }

    // ------------------------------------------------------------------------

    /**
     * BBCode::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \JBBCode\Parser) {
            return true;
        }

        return false;
    }
}