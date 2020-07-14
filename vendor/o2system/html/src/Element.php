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

namespace O2System\Html;

// ------------------------------------------------------------------------

use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Element
 *
 * @package O2System\Html
 */
class Element
{
    /**
     * Element::$tagName
     *
     * @var string
     */
    public $tagName;

    /**
     * Element::$entity
     *
     * @var \O2System\Html\Element\Entity
     */
    public $entity;

    /**
     * Element::$attributes
     *
     * @var \O2System\Html\Element\Attribute
     */
    public $attributes;

    /**
     * Element::$textContent
     *
     * @var \O2System\Html\Element\TextContent
     */
    public $textContent;

    /**
     * Element::$childNodes
     *
     * @var \O2System\Html\Element\Nodes
     */
    public $childNodes;

    /**
     * Element::$metadata
     *
     * @var \O2System\Html\Element\Metadata
     */
    public $metadata;

    // ------------------------------------------------------------------------

    /**
     * Element::__construct
     *
     * @param string      $tagName
     * @param string|null $entityName
     */
    public function __construct($tagName, $entityName = null)
    {
        $this->tagName = trim($tagName);

        $this->entity = new Element\Entity();
        $this->entity->setEntityName($entityName);

        $this->attributes = new Element\Attributes();
        $this->textContent = new Element\TextContent();
        $this->childNodes = new Element\Nodes();
        $this->metadata = new Element\Metadata();
    }

    // ------------------------------------------------------------------------

    /**
     * Element::__clone
     *
     * @return \O2System\Html\Element
     * @throws \ReflectionException
     */
    public function __clone()
    {
        $newElement = $this;
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $value = $property->getValue($newElement);

            if (is_object($value)) {
                if ($value instanceof ArrayIterator) {
                    $value = new ArrayIterator($value->getArrayCopy());
                    $property->setValue($newElement, $value);
                } else {
                    $property->setValue($newElement, clone $value);
                }
            } else {
                $property->setValue($newElement, $value);
            }
        }

        return $newElement;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    // ------------------------------------------------------------------------

    /**
     * Element::render
     *
     * @return string
     */
    public function render()
    {
        $selfClosingTags = [
            'area',
            'base',
            'br',
            'col',
            'command',
            'embed',
            'hr',
            'img',
            'input',
            'keygen',
            'link',
            'meta',
            'param',
            'source',
            'track',
            'wbr',
        ];

        if (in_array($this->tagName, $selfClosingTags)) {
            $attr = $this->attributes;
            unset($attr[ 'realpath' ]);

            if ($this->hasAttributes()) {
                return '<' . $this->tagName . ' ' . trim($this->attributes->__toString()) . '>';
            }

            return '<' . $this->tagName . '>';
        } else {
            $output[] = $this->open();

            if ($this->hasTextContent()) {
                $output[] = PHP_EOL . implode('', $this->textContent->getArrayCopy()) . PHP_EOL;
            }

            if ($this->hasChildNodes()) {
                if ( ! $this->hasTextContent()) {
                    $output[] = PHP_EOL;
                }

                foreach ($this->childNodes as $childNode) {
                    $output[] = $childNode . PHP_EOL;
                }
            }
        }

        $output[] = $this->close();

        return implode('', $output);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::hasAttributes
     *
     * @return bool
     */
    public function hasAttributes()
    {
        return (bool)($this->attributes->count() == 0 ? false : true);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::open
     *
     * @return string
     */
    public function open()
    {
        $attr = $this->attributes;
        unset($attr[ 'realpath' ]);

        if ($this->hasAttributes()) {
            return '<' . $this->tagName . ' ' . trim($this->attributes->__toString()) . '>';
        }

        return '<' . $this->tagName . '>';
    }

    // ------------------------------------------------------------------------

    /**
     * Element::hasTextContent
     *
     * @return bool
     */
    public function hasTextContent()
    {
        return (bool)($this->textContent->count() == 0 ? false : true);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::hasChildNodes
     *
     * @return bool
     */
    public function hasChildNodes()
    {
        return (bool)($this->childNodes->count() == 0 ? false : true);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::close
     *
     * @return string
     */
    public function close()
    {
        return '</' . $this->tagName . '>';
    }
}