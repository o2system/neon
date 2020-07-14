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

namespace O2System\Html\Dom\Lists;

// ------------------------------------------------------------------------

use O2System\Html\Dom\Element;
use RecursiveIterator;

/**
 * Class Node
 *
 * @package O2System\HTML\DOM\Lists
 */
class Nodes extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * Nodes::$length
     *
     * @var int
     */
    public $length = 0;

    // ------------------------------------------------------------------------

    /**
     * Nodes::__construct
     *
     * @param \DOMNodeList $nodeList
     */
    public function __construct(\DOMNodeList $nodeList)
    {
        $nodes = [];

        foreach ($nodeList as $node) {
            $this->length++;
            $nodes[] = $node;
        }

        parent::__construct($nodes);
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::item
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function item($offset)
    {
        return $this->offsetGet($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::hasChildren
     *
     * Returns if an iterator can be created for the current entry.
     *
     * @link  http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::getChildren
     *
     * Returns an iterator for the current entry.
     *
     * @link  http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        return new self($this->current()->childNodes);
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::replace
     *
     * @param mixed $source
     */
    public function replace($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->replace($source);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::remove
     */
    public function remove()
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                if ( ! empty($node->parentNode)) {
                    $node->parentNode->removeChild($node);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::prepend
     *
     * @param mixed $source
     *
     * @return static
     */
    public function prepend($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->append($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::append
     *
     * @param mixed $source
     *
     * @return static
     */
    public function append($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->prepend($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::before
     *
     * @param mixed $source
     *
     * @return static
     */
    public function before($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->before($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::after
     *
     * @param mixed $source
     *
     * @return static
     */
    public function after($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->after($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::__empty
     *
     * @return static
     */
    public function __empty()
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->empty();
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::__clone
     *
     * @return \O2System\Html\Dom\Lists\Nodes
     */
    public function __clone()
    {
        return clone $this;
    }
}