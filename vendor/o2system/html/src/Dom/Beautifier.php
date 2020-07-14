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

namespace O2System\Html\Dom;

// ------------------------------------------------------------------------

/**
 * Class Beautifier
 *
 * @package O2System\HTML\DOM
 */
class Beautifier
{
    /**
     * Beautifier::ELEMENT_TYPE_BLOCK
     *
     * HTML Element Type Block Constant
     *
     * @var int
     */
    const ELEMENT_TYPE_BLOCK = 0;

    /**
     * Beautifier::ELEMENT_TYPE_INLINE
     *
     * HTML Element Type Inline Constant
     *
     * @var int
     */
    const ELEMENT_TYPE_INLINE = 1;

    /**
     * Beautifier::MATCH_INDENT_NO
     *
     * HTML Element No Indent
     *
     * @var int
     */
    const MATCH_INDENT_NO = 0;

    /**
     * Beautifier::MATCH_INDENT_DECREASE
     *
     * HTML Element Indent Decrease
     *
     * @var int
     */
    const MATCH_INDENT_DECREASE = 1;

    /**
     * Beautifier::MATCH_INDENT_INCREASE
     *
     * HTML Element Indent Increase
     *
     * @var int
     */
    const MATCH_INDENT_INCREASE = 2;

    /**
     * Beautifier::MATCH_DISCARD
     *
     * HTML Element Indent Discard
     *
     * @var int
     */
    const MATCH_DISCARD = 3;

    /**
     * Beautifier::$indentCharacter
     *
     * Indentation Character
     *
     * @var string
     */
    private $indentCharacter = '    ';

    /**
     * Beautifier::$inlineElements
     *
     * Inline Elements
     *
     * @var array
     */
    private $inlineElements = [
        'b',
        'big',
        'i',
        'small',
        'tt',
        'abbr',
        'acronym',
        'cite',
        'code',
        'dfn',
        'em',
        'kbd',
        'strong',
        'samp',
        'var',
        'a',
        'bdo',
        'br',
        'img',
        'span',
        'sub',
        'sup',
    ];

    // ------------------------------------------------------------------------

    /**
     * Beautifier::setElementType
     *
     * @param string $elementName
     * @param int    $type FormatOutput::ELEMENT_TYPE_BLOCK | FormatOutput::ELEMENT_TYPE_INLINE
     */
    public function setElementType($elementName, $type)
    {
        if ($type === static::ELEMENT_TYPE_BLOCK) {
            $this->inlineElements = array_diff($this->inlineElements, [$elementName]);
        } else {
            if ($type === static::ELEMENT_TYPE_INLINE) {
                $this->inlineElements[] = $elementName;
            }
        }

        if ($this->inlineElements) {
            $this->inlineElements = array_unique($this->inlineElements);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Beautifier::format
     *
     * @param $source
     *
     * @return string
     */
    public function format($source)
    {
        // We does not indent <script> body. Instead, it temporary removes it from the code, indents the input, and restores the script body.
        $tempScriptElements = [];

        if (preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', $source, $matches)) {
            $tempScriptElements = $matches[ 0 ];

            foreach ($matches[ 0 ] as $i => $match) {
                $source = str_replace($match, '<script>' . ($i + 1) . '</script>', $source);
            }
        }

        // Removing double whitespaces to make the source code easier to read.
        // With exception of <pre>/ CSS white-space changing the default behaviour, double whitespace is meaningless in HTML output.
        // This reason alone is sufficient not to use indentation in production.
        $source = str_replace("\t", '', $source);
        $source = preg_replace('/\s{2,}/', ' ', $source);

        // Remove inline elements and replace them with text entities.
        $tempInlineElements = [];

        if (preg_match_all(
            '/<(' . implode('|', $this->inlineElements) . ')[^>]*>(?:[^<]*)<\/\1>/',
            $source,
            $matches
        )) {
            $tempInlineElements = $matches[ 0 ];

            foreach ($matches[ 0 ] as $i => $match) {
                $source = str_replace($match, 'ᐃ' . ($i + 1) . 'ᐃ', $source);
            }
        }

        $output = '';

        $nextLineIndentationLevel = 0;

        do {
            $indentationLevel = $nextLineIndentationLevel;

            $patterns = [
                // block tag
                '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\2)>)/'  => static::MATCH_INDENT_NO,
                // DOCTYPE
                '/^<!([^>]*)>/'                                => static::MATCH_INDENT_NO,
                // tag with implied closing
                '/^<(input|link|meta|base|br|img|hr)([^>]*)>/' => static::MATCH_INDENT_NO,
                // opening tag
                '/^<[^\/]([^>]*)>/'                            => static::MATCH_INDENT_INCREASE,
                // closing tag
                '/^<\/([^>]*)>/'                               => static::MATCH_INDENT_DECREASE,
                // self-closing tag
                '/^<(.+)\/>/'                                  => static::MATCH_INDENT_DECREASE,
                // whitespace
                '/^(\s+)/'                                     => static::MATCH_DISCARD,
                // text node
                '/([^<]+)/'                                    => static::MATCH_INDENT_NO,
            ];

            foreach ($patterns as $pattern => $rule) {
                if ($match = preg_match($pattern, $source, $matches)) {
                    if (function_exists('mb_substr')) {
                        $source = mb_substr($source, mb_strlen($matches[ 0 ]));
                    } else {
                        $source = substr($source, strlen($matches[ 0 ]));
                    }

                    if ($rule === static::MATCH_DISCARD) {
                        break;
                    }

                    if ($rule === static::MATCH_INDENT_NO) {

                    } else {
                        if ($rule === static::MATCH_INDENT_DECREASE) {
                            $nextLineIndentationLevel--;
                            $indentationLevel--;
                        } else {
                            $nextLineIndentationLevel++;
                        }
                    }

                    if ($indentationLevel < 0) {
                        $indentationLevel = 0;
                    }

                    $output .= str_repeat($this->indentCharacter, $indentationLevel) . $matches[ 0 ] . "\n";

                    break;
                }
            }
        } while ($match);

        $output = preg_replace('/(<(\w+)[^>]*>)\s*(<\/\2>)/', '\\1\\3', $output);

        foreach ($tempScriptElements as $i => $original) {
            $output = str_replace('<script>' . ($i + 1) . '</script>', $original, $output);
        }

        foreach ($tempInlineElements as $i => $original) {
            $output = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $output);
        }

        return trim($output);
    }
}