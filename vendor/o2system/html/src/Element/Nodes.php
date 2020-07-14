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

namespace O2System\Html\Element;

// ------------------------------------------------------------------------

use O2System\Html\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Nodes
 *
 * @package O2System\Html\Element
 */
class Nodes extends ArrayIterator
{
    /**
     * Nodes::$nodesEntities
     *
     * @var array
     */
    private $nodesEntities = [];

    // ------------------------------------------------------------------------

    /**
     * Nodes::createNode
     *
     * @param string        $tagName
     * @param string|null   $entityName
     *
     * @return mixed
     */
    public function createNode($tagName, $entityName = null)
    {
        if ($tagName instanceof Element) {
            $this->push($tagName);
        } else {
            $this->push(new Element($tagName, $entityName));
        }

        return $this->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::push
     *
     * @param mixed $value
     */
    public function push($value)
    {
        parent::push($value);
        $this->nodesEntities[] = $this->last()->entity->getEntityName();
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::hasNode
     *
     * @param string $index
     *
     * @return bool
     */
    public function hasNode($index)
    {
        if (is_string($index) and in_array($index, $this->nodesEntities)) {
            if (false !== ($key = array_search($index, $this->nodesEntities))) {
                if ($this->offsetExists($key)) {
                    return true;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::getNode
     *
     * @param string $index
     *
     * @return bool|mixed
     */
    public function getNode($index)
    {
        if (is_string($index) and in_array($index, $this->nodesEntities)) {
            if (false !== ($key = array_search($index, $this->nodesEntities))) {
                if ($this->offsetExists($key)) {
                    return $this->offsetGet($index);
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::item
     *
     * @param string $index
     *
     * @return mixed
     */
    public function item($index)
    {
        if($this->offsetExists($index)) {
            return $this->offsetGet($index);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::prepend
     *
     * @param mixed $value
     */
    public function prepend($value)
    {
        parent::unshift($value);
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::getNodeByTagName
     *
     * @param string $tagName
     *
     * @return array
     */
    public function getNodeByTagName($tagName)
    {
        $result = [];

        foreach ($this as $node) {
            if ($node->tagName === $tagName) {
                $result[] = $node;
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Nodes::getNodeByEntityName
     *
     * @param string $entityName
     *
     * @return bool|mixed
     */
    public function getNodeByEntityName($entityName)
    {
        if (false !== ($index = array_search($entityName, $this->nodesEntities))) {
            if ($this->offsetExists($index)) {
                return $this->offsetGet($index);
            }
        }

        return false;
    }
}