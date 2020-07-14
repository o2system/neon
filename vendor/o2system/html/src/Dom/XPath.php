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

use InvalidArgumentException;
use O2System\Html\Dom\Lists\Nodes;
use RuntimeException;

/**
 * Class XPath
 *
 * @package O2System\HTML\DOM
 */
class XPath extends \DOMXPath
{
    /**
     * XPath Compiled Expressions
     *
     * @var array
     */
    private $compiledExpressions = [];

    // ------------------------------------------------------------------------

    /**
     * XPath::query
     *
     * Evaluates the given XPath expression.
     *
     * @see   http://php.net/manual/en/domxpath.query.php
     *
     * @param string   $expression <p>
     *                             The XPath expression to execute.
     *                             </p>
     * @param \DOMNode $context    [optional] <p>
     *                             The optional node context can be specified for
     *                             doing relative XPath queries. By default, the queries are relative to
     *                             the root element.
     *                             </p>
     *
     * @return Nodes a DOMNodeList containing all nodes matching
     * the given XPath expression. Any expression which do
     * not return nodes will return an empty DOMNodeList.
     * @since 5.0
     */
    public function query($expression, \DOMNode $context = null, $registerNodeNS = null)
    {
        if (strpos($expression, '/') === false) {
            $expression = $this->fetchExpression($expression);
        }

        return new Lists\Nodes(parent::query($expression, $context));
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchExpression
     *
     * @param string $expression
     *
     * @return string
     */
    private function fetchExpression($expression)
    {
        $selectors = explode(',', $expression);
        $paths = [];

        foreach ($selectors as $selector) {
            $selector = trim($selector);

            if (array_key_exists($selector, $this->compiledExpressions)) {
                $paths[] = $this->compiledExpressions[ $selector ];

                continue;
            }

            $this->compiledExpressions[ $selector ] = $this->fetchCssExpression($selector);

            $paths[] = $this->compiledExpressions[ $selector ];
        }

        return implode('|', $paths);
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssExpression
     *
     * Converts a CSS selector into an XPath expression.
     *
     * @param string $selector A CSS selector
     * @param string $prefix   Specifies the nesting of nodes
     *
     * @return string XPath expression
     */
    private function fetchCssExpression($selector, $prefix = '//')
    {
        $pos = strrpos($selector, '::');

        if ($pos !== false) {
            $property = substr($selector, $pos + 2);
            $property = $this->fetchCssProperty($property);
            $property = $this->parseCssProperty($property[ 'name' ], $property[ 'args' ]);

            $selector = substr($selector, 0, $pos);
        }

        if (substr($selector, 0, 1) === '>') {
            $prefix = '/';

            $selector = ltrim($selector, '> ');
        }

        $segments = $this->getSelectorSegments($selector);
        $expression = '';

        while (count($segments) > 0) {
            $expression .= $this->generateExpression($segments, $prefix);

            $selector = trim(substr($selector, strlen($segments[ 'selector' ])));
            $prefix = isset($segments[ 'rel' ]) ? '/' : '//';

            if ($selector === '') {
                break;
            }

            $segments = $this->getSelectorSegments($selector);
        }

        if (isset($property)) {
            $expression = $expression . '/' . $property;
        }

        return $expression;
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssProperty
     *
     * @param $property
     *
     * @return array
     */
    protected function fetchCssProperty($property)
    {
        $name = '(?P<name>[\w\-]*)';
        $args = '(?:\((?P<args>[^\)]+)\))';
        $regexp = '/(?:' . $name . $args . '?)?/is';

        if (preg_match($regexp, $property, $segments)) {
            $result = [];

            $result[ 'name' ] = $segments[ 'name' ];
            $result[ 'args' ] = isset($segments[ 'args' ]) ? explode('|', $segments[ 'args' ]) : [];

            return $result;
        }

        throw new RuntimeException('Invalid selector');
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::parseCssProperty
     *
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    protected function parseCssProperty($name, $args = [])
    {
        if ($name === 'text') {
            return 'text()';
        }

        if ($name === 'attr') {
            $attributes = [];

            foreach ($args as $attribute) {
                $attributes[] = sprintf('name() = "%s"', $attribute);
            }

            return sprintf('@*[%s]', implode(' or ', $attributes));
        }

        throw new RuntimeException('HTML_E_INVALID_CSS_PROPERTY');
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::getSelectorSegments
     *
     * Splits the CSS selector into parts (tag name, ID, classes, attributes, pseudo-class).
     *
     * @param string $selector CSS selector
     *
     * @return array
     *
     * @throws \InvalidArgumentException if an empty string is passed
     * @throws \RuntimeException if the selector is not valid
     */
    public function getSelectorSegments($selector)
    {
        $selector = trim($selector);

        if ($selector === '') {
            throw new InvalidArgumentException('HTML_E_INVALID_SELECTOR');
        }

        $tag = '(?P<tag>[\*|\w|\-]+)?';
        $id = '(?:#(?P<id>[\w|\-]+))?';
        $classes = '(?P<classes>\.[\w|\-|\.]+)*';
        $attrs = '(?P<attrs>\[.+\])*';
        $name = '(?P<pseudo>[\w\-]*)';
        $expr = '(?:\((?P<expr>[^\)]+)\))';
        $pseudo = '(?::' . $name . $expr . '?)?';
        $rel = '\s*(?P<rel>>)?';

        $regexp = '/' . $tag . $id . $classes . $attrs . $pseudo . $rel . '/is';

        if (preg_match($regexp, $selector, $segments)) {
            if ($segments[ 0 ] === '') {
                throw new RuntimeException('HTML_E_INVALID_SELECTOR');
            }

            $result[ 'selector' ] = $segments[ 0 ];
            $result[ 'tag' ] = (isset($segments[ 'tag' ]) and $segments[ 'tag' ] !== '') ? $segments[ 'tag' ] : '*';

            // if the id attribute specified
            if (isset($segments[ 'id' ]) and $segments[ 'id' ] !== '') {
                $result[ 'id' ] = $segments[ 'id' ];
            }

            // if the attributes specified
            if (isset($segments[ 'attrs' ])) {
                $attributes = trim($segments[ 'attrs' ], '[]');
                $attributes = explode('][', $attributes);

                foreach ($attributes as $attribute) {
                    if ($attribute !== '') {
                        list($name, $value) = array_pad(explode('=', $attribute, 2), 2, null);

                        // equal null if specified only the attribute name
                        $result[ 'attributes' ][ $name ] = is_string($value) ? trim($value, '\'"') : null;
                    }
                }
            }

            // if the class attribute specified
            if (isset($segments[ 'classes' ])) {
                $classes = trim($segments[ 'classes' ], '.');
                $classes = explode('.', $classes);

                foreach ($classes as $class) {
                    if ($class !== '') {
                        $result[ 'classes' ][] = $class;
                    }
                }
            }

            // if the pseudo class specified
            if (isset($segments[ 'pseudo' ]) and $segments[ 'pseudo' ] !== '') {
                $result[ 'pseudo' ] = $segments[ 'pseudo' ];

                if (isset($segments[ 'expr' ]) and $segments[ 'expr' ] !== '') {
                    $result[ 'expr' ] = $segments[ 'expr' ];
                }
            }

            // if it is a direct descendant
            if (isset($segments[ 'rel' ])) {
                $result[ 'rel' ] = $segments[ 'rel' ];
            }

            return $result;
        }

        throw new RuntimeException('HTML_E_INVALID_SELECTOR');
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::generateExpression
     *
     * @param array  $segments
     * @param string $prefix Specifies the nesting of nodes
     *
     * @return string XPath expression
     *
     * @throws InvalidArgumentException if you neither specify tag name nor attributes
     */
    private function generateExpression($segments, $prefix = '//')
    {
        $tagName = isset($segments[ 'tag' ]) ? $segments[ 'tag' ] : '*';

        $attributes = [];

        // if the id attribute specified
        if (isset($segments[ 'id' ])) {
            $attributes[] = sprintf('@id="%s"', $segments[ 'id' ]);
        }

        // if the class attribute specified
        if (isset($segments[ 'classes' ])) {
            foreach ($segments[ 'classes' ] as $class) {
                $attributes[] = sprintf('contains(concat(" ", normalize-space(@class), " "), " %s ")', $class);
            }
        }

        // if the attributes specified
        if (isset($segments[ 'attributes' ])) {
            foreach ($segments[ 'attributes' ] as $name => $value) {
                $attributes[] = $this->fetchCssAttributeSelector($name, $value);
            }
        }

        // if the pseudo class specified
        if (isset($segments[ 'pseudo' ])) {
            $expression = isset($segments[ 'expr' ]) ? trim($segments[ 'expr' ]) : '';

            $parameters = explode(',', $expression);

            $attributes[] = $this->fetchCssPseudoSelector($segments[ 'pseudo' ], $parameters, $tagName);
        }

        if (count($attributes) === 0 and ! isset($segments[ 'tag' ])) {
            throw new InvalidArgumentException(
                'The array of segments should contain the name of the tag or at least one attribute'
            );
        }

        $xpath = $prefix . $tagName;

        if ($count = count($attributes)) {
            $xpath .= ($count > 1)
                ? sprintf('[(%s)]', implode(') and (', $attributes))
                : sprintf(
                    '[%s]',
                    $attributes[ 0 ]
                );
        }

        return $xpath;
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssAttributeSelector
     *
     * @param string $name  The attribute name
     * @param string $value The attribute value
     *
     * @return string
     */
    protected function fetchCssAttributeSelector($name, $value)
    {
        // if the attribute name starts with ^
        // example: *[^data-]
        if (substr($name, 0, 1) === '^') {
            $xpath = sprintf('@*[starts-with(name(), "%s")]', substr($name, 1));

            return $value === null ? $xpath : sprintf('%s="%s"', $xpath, $value);
        }

        // if the attribute name starts with !
        // example: input[!disabled]
        if (substr($name, 0, 1) === '!') {
            $xpath = sprintf('not(@%s)', substr($name, 1));

            return $xpath;
        }

        switch (substr($name, -1)) {
            case '^':
                $xpath = sprintf('starts-with(@%s, "%s")', substr($name, 0, -1), $value);
                break;
            case '$':
                $xpath = sprintf('ends-with(@%s, "%s")', substr($name, 0, -1), $value);
                break;
            case '*':
                $xpath = sprintf('contains(@%s, "%s")', substr($name, 0, -1), $value);
                break;
            case '!':
                $xpath = sprintf('not(@%s="%s")', substr($name, 0, -1), $value);
                break;
            case '~':
                $xpath = sprintf(
                    'contains(concat(" ", normalize-space(@%s), " "), " %s ")',
                    substr($name, 0, -1),
                    $value
                );
                break;
            default:
                // if specified only the attribute name
                $xpath = $value === null ? '@' . $name : sprintf('@%s="%s"', $name, $value);
                break;
        }

        return $xpath;
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssPseudoSelector
     *
     * Converts a CSS pseudo-class into an XPath expression.
     *
     * @param string $pseudo Pseudo-class
     * @param array  $parameters
     * @param string $tagName
     *
     * @return string
     *
     * @throws \RuntimeException if passed an unknown pseudo-class
     */
    protected function fetchCssPseudoSelector($pseudo, $parameters = [], &$tagName)
    {
        switch ($pseudo) {
            case 'first-child':
                return 'position() = 1';
                break;
            case 'last-child':
                return 'position() = last()';
                break;
            case 'nth-child':
                $xpath = sprintf(
                    '(name()="%s") and (%s)',
                    $tagName,
                    $this->fetchCssPseudoNthSelector($parameters[ 0 ])
                );
                $tagName = '*';

                return $xpath;
                break;
            case 'contains':
                $string = trim($parameters[ 0 ], ' \'"');
                $caseSensitive = isset($parameters[ 1 ]) and (trim($parameters[ 1 ]) === 'true');

                return $this->fetchCssPseudoContainsSelector($string, $caseSensitive);
                break;
            case 'has':
                return $this->fetchCssExpression($parameters[ 0 ], './/');
                break;
            case 'not':
                return sprintf('not($this->%s)', $this->fetchCssExpression($parameters[ 0 ], ''));
                break;
            case 'nth-of-type':
                return $this->fetchCssPseudoNthSelector($parameters[ 0 ]);
                break;
            case 'empty':
                return 'count(descendant::*) = 0';
                break;
            case 'not-empty':
                return 'count(descendant::*) > 0';
                break;
        }

        throw new RuntimeException('Invalid selector: unknown pseudo-class');
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssPseudoNthSelector
     *
     * Converts nth-expression into an XPath expression.
     *
     * @param string $expression nth-expression
     *
     * @return string
     *
     * @throws \RuntimeException if passed nth-child is empty
     * @throws \RuntimeException if passed an unknown nth-child expression
     */
    protected function fetchCssPseudoNthSelector($expression)
    {
        if ($expression === '') {
            throw new RuntimeException(
                'Invalid selector: nth-child (or nth-last-child) expression must not be empty'
            );
        }

        if ($expression === 'odd') {
            return 'position() mod 2 = 1 and position() >= 1';
        }

        if ($expression === 'even') {
            return 'position() mod 2 = 0 and position() >= 0';
        }

        if (is_numeric($expression)) {
            return sprintf('position() = %d', $expression);
        }

        if (preg_match("/^(?P<mul>[0-9]?n)(?:(?P<sign>\+|\-)(?P<pos>[0-9]+))?$/is", $expression, $segments)) {
            if (isset($segments[ 'mul' ])) {
                $multiplier = $segments[ 'mul' ] === 'n' ? 1 : trim($segments[ 'mul' ], 'n');
                $sign = (isset($segments[ 'sign' ]) and $segments[ 'sign' ] === '+') ? '-' : '+';
                $position = isset($segments[ 'pos' ]) ? $segments[ 'pos' ] : 0;

                return sprintf(
                    '(position() %s %d) mod %d = 0 and position() >= %d',
                    $sign,
                    $position,
                    $multiplier,
                    $position
                );
            }
        }

        throw new RuntimeException('Invalid selector: invalid nth-child expression');
    }

    // ------------------------------------------------------------------------

    /**
     * XPath::fetchCssPseudoContainsSelector
     *
     * @param string $string
     * @param bool   $caseSensitive
     *
     * @return string
     */
    protected function fetchCssPseudoContainsSelector($string, $caseSensitive = false)
    {
        if ($caseSensitive) {
            return sprintf('text() = "%s"', $string);
        }

        if (function_exists('mb_strtolower')) {
            return sprintf(
                'php:functionString("mb_strtolower", .) = php:functionString("mb_strtolower", "%s")',
                $string
            );
        } else {
            return sprintf('php:functionString("strtolower", .) = php:functionString("strtolower", "%s")', $string);
        }
    }
}