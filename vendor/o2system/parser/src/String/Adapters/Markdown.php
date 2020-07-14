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
 * Class Markdown
 *
 * This class driver for Parse Markdown Code for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class Markdown extends AbstractAdapter
{
    /**
     * Markdown::MARKDOWN_BASIC
     *
     * @var int
     */
    const MARKDOWN_BASIC = 0;

    /**
     * Markdown::MARKDOWN_GITHUB
     *
     * @var int
     */
    const MARKDOWN_GITHUB = 1;

    /**
     * Markdown::MARKDOWN_EXTRA
     *
     * @var int
     */
    const MARKDOWN_EXTRA = 2;

    /**
     * Markdown::$flavour
     *
     * @var int
     */
    private $flavour = 0;

    // ------------------------------------------------------------------------

    /**
     * Markdown::initialize
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
                $this->engine = \cebe\markdown\Markdown();

                if (isset($config[ 'flavour' ])) {
                    $this->setFlavour($config[ 'flavour' ]);
                }
            } else {
                throw new RuntimeException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['Markdown Parser by Carsten Brandt', 'https://github.com/cebe/markdown']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Markdown::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\cebe\markdown\Markdown')) {
            return true;
        }

        return false;
    }

    public function setFlavour($flavour)
    {
        if (is_int($flavour) AND in_array($flavour, range(0, 2))) {
            $this->flavour = $flavour;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Markdown::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        switch ($this->flavour) {
            default:
            case self::MARKDOWN_BASIC:

                return $this->engine->parse($this->string);

                break;

            case self::MARKDOWN_GITHUB:

                $parser = new \cebe\markdown\GithubMarkdown();

                return $parser->parse($this->string);

                break;

            case self::MARKDOWN_EXTRA:

                $parser = new \cebe\markdown\MarkdownExtra();

                return $parser->parse($this->string);

                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Markdown::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \cebe\markdown\Markdown) {
            return true;
        }

        return false;
    }
}