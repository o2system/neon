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

namespace O2System\Parser\String\Engines;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;
use O2System\Spl\Patterns\Structural\Provider\ValidationInterface;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;
use O2System\Spl\Traits\Collectors\FileExtensionCollectorTrait;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class Shortcodes
 *
 * This parser engine is used to parse WordPress "shortcodes".
 * The tag and attribute parsing or regular expression code is
 * based on the TextPattern tag parser.
 *
 * A few examples are below:
 *
 * [shortcode /]
 * [shortcode foo="bar" baz="bing" /]
 * [shortcode foo="bar"]content[/shortcode]
 *
 * Shortcode tags support attributes and enclosed content, but does not entirely
 * support inline shortcodes in other shortcodes. You will have to call the
 * shortcode parser in your function to account for that.
 *
 * @package O2System\Parser\Template\Engines
 */
class Shortcodes extends AbstractProvider implements
    ValidationInterface
{
    use FileExtensionCollectorTrait;
    use FilePathCollectorTrait;
    use ConfigCollectorTrait;

    /**
     * Shortcodes::$config
     *
     * Shortcodes engine configurations.
     *
     * @var array
     */
    protected $config;

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::__construct
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::parseFile
     *
     * @param string $filePath
     * @param array  $vars
     *
     * @return string
     */
    public function parseFile($filePath, array $vars = [])
    {
        if (is_file($filePath)) {
            return $this->parseString(file_get_contents($filePath), $vars);
        }

        // Try to find from filePaths
        if (count($this->filePaths)) {
            foreach ($this->filePaths as $fileDirectory) {
                if (is_file($fileDirectory . $filePath)) {
                    return $this->parseString(file_get_contents($fileDirectory . $filePath), $vars);
                    break;
                }
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::parseString
     *
     * @param string $source
     * @param array  $vars
     *
     * @return mixed
     */
    public function parseString($source, array $vars = [])
    {
        if (count($vars)) {
            foreach ($vars as $offset => $shortcode) {
                $this->register($shortcode, $offset);
            }
        }

        $pattern = $this->getRegex();

        return preg_replace_callback('/' . $pattern . '/s', [&$this, 'parseRegex'], $source);
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::getRegex
     *
     * Retrieve the shortcode regular expression for searching.
     *
     * The regular expression combines the shortcode tags in the regular expression
     * in a regex class.
     *
     * The regular expresion contains 6 different sub matches to help with parsing.
     *
     * 1/6 - An extra [ or ] to allow for escaping shortcodes with double [[]]
     * 2 - The shortcode name
     * 3 - The shortcode argument list
     * 4 - The self closing /
     * 5 - The content of a shortcode when it wraps some content.
     *
     * @return string The shortcode search regular expression
     */
    private function getRegex()
    {
        $shortcodes = $this->getIterator();

        $offsetKeys = $shortcodes->getKeys();
        $offsetRegex = join('|', array_map('preg_quote', $offsetKeys));

        // WARNING! Do not change this regex
        return '(.?)\[(' . $offsetRegex . ')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::validate
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value)
    {
        return (bool)is_callable($value);
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::parseRegex
     *
     * Regular Expression callable for Shortcodes::parseString for calling shortcode hook.
     *
     * @see    Shortcodes::getRegex for details of the match array contents.
     *
     * @since  1.0
     * @access private
     * @uses   $shortcode_tags
     *
     * @param array $match Regular expression match array
     *
     * @return mixed False on failure.
     */
    private function parseRegex($match)
    {
        // allow [[foo]] syntax for escaping a tag
        if ($match[ 1 ] == '[' && $match[ 6 ] == ']') {
            return substr($match[ 0 ], 1, -1);
        }

        $offset = $match[ 2 ];
        $attr = $this->parseRegexAttributes($match[ 3 ]);

        if ($this->exists($offset)) {
            if (isset($match[ 5 ])) {
                // enclosing tag - extra parameter
                return $match[ 1 ] . call_user_func(
                        $this->__get($offset),
                        $attr,
                        $match[ 5 ],
                        $offset
                    ) . $match[ 6 ];
            } else {
                // self-closing tag
                return $match[ 1 ] . call_user_func($this->__get($offset), $attr, null, $offset) . $match[ 6 ];
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Shortcodes::parseRegexAttr
     *
     * Retrieve all attributes from the shortcodes tag.
     *
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     *
     * @since 1.0
     *
     * @param string $string
     *
     * @return array List of attributes and their value.
     */
    private function parseRegexAttributes($string)
    {
        $attr = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $string = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $string);
        if (preg_match_all($pattern, $string, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if ( ! empty($m[ 1 ])) {
                    $attr[ strtolower($m[ 1 ]) ] = stripcslashes($m[ 2 ]);
                } elseif ( ! empty($m[ 3 ])) {
                    $attr[ strtolower($m[ 3 ]) ] = stripcslashes($m[ 4 ]);
                } elseif ( ! empty($m[ 5 ])) {
                    $attr[ strtolower($m[ 5 ]) ] = stripcslashes($m[ 6 ]);
                } elseif (isset($m[ 7 ]) and strlen($m[ 7 ])) {
                    $attr[] = stripcslashes($m[ 7 ]);
                } elseif (isset($m[ 8 ])) {
                    $attr[] = stripcslashes($m[ 8 ]);
                }
            }
        } else {
            $attr = ltrim($string);
        }

        return $attr;
    }
}