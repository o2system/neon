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

use O2System\Html\Document;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;

/**
 * Class Element
 *
 * @package O2System\HTML\DOM
 */
class Element extends \DOMElement
{
    /**
     * Element::__get
     *
     * Returns the value for the property specified
     *
     * @param string $name The name of the property
     *
     * @return string The value of the property specified
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if ( ! is_string($name)) {
            throw new InvalidArgumentException('HTML_E_INVALID_ARGUMENT', 0, ['string']);
        }
        if ($name === 'innerHTML') {
            $html = $this->ownerDocument->saveHTML($this);
            $nodeName = $this->nodeName;

            return preg_replace('@^<' . $nodeName . '[^>]*>|</' . $nodeName . '>$@', '', $html);
        } elseif ($name === 'outerHTML') {
            return $this->ownerDocument->saveHTML($this);
        } else {
            throw new InvalidArgumentException('HTML_E_INVALID_ARGUMENT', 0, ['HTMLDOMElement::$' . $name]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Element::__set
     *
     * Sets the value for the property specified
     *
     * @param string $name
     * @param string $value
     *
     * @throws \InvalidArgumentException
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     */
    public function __set($name, $value)
    {
        if ( ! is_string($name)) {
            throw new InvalidArgumentException('HTML_E_INVALID_ARGUMENT', 0, ['string']);
        }

        if ( ! is_string($value)) {
            throw new InvalidArgumentException('HTML_E_INVALID_ARGUMENT', 0, ['string']);
        }

        if ($name === 'innerHTML') {
            while ($this->hasChildNodes()) {
                $this->removeChild($this->firstChild);
            }

            $DOMDocument = new Document();
            $DOMDocument->loadHTML('<body>' . $value . '</body>');

            foreach ($DOMDocument->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->appendChild($node);
            }
        } elseif ($name === 'outerHTML') {
            $DOMDocument = new Document();
            $DOMDocument->loadHTML('<body>' . $value . '</body>');
            foreach ($DOMDocument->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->parentNode->insertBefore($node, $this);
            }

            $this->parentNode->removeChild($this);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Element::addAttributes
     *
     * @param array $attr
     *
     * @return static
     */
    public function addAttributes(array $attr)
    {
        foreach ($attr as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::getAttributes
     *
     * Returns an array containing all attributes
     *
     * @return array An associative array containing all attributes
     */
    public function getAttributes()
    {
        $attributesCount = $this->attributes->length;
        $attributes = [];

        for ($i = 0; $i < $attributesCount; $i++) {
            $attributes[] = $this->attributes->item($i);
        }

        return $attributes;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::clone
     *
     * @return \O2System\Html\Dom\Element
     */
    public function clones()
    {
        return clone $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::__toString
     *
     * Returns the element outerHTML
     *
     * @return string The element outerHTML
     */
    public function __toString()
    {
        return $this->outerHTML;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::clear
     *
     * @return void
     */
    public function clear()
    {
        if ($this->childNodes->length > 0) {
            foreach ($this->childNodes as $childNode) {
                $this->removeChild($childNode);
            }
        }

        $this->textContent = null;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::html
     *
     * @param null $newInnerHTML
     *
     * @return string
     */
    public function html($newInnerHTML = null)
    {
        if (isset($newInnerHTML)) {
            $this->replace($newInnerHTML);
        }

        return $this->innerHTML;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::replace
     *
     * @param $source
     *
     * @return \DOMNode
     */
    public function replace($source)
    {
        $importNode = $this->ownerDocument->importNode($this->ownerDocument->importSourceNode($source), true);

        return $this->parentNode->replaceChild($importNode, $this);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::text
     *
     * @param null $newTextContent
     *
     * @return null
     */
    public function text($newTextContent = null)
    {
        if (isset($newTextContent)) {
            $this->textContent = $this->nodeValue = $newTextContent;
        }

        return $this->textContent;
    }

    // ------------------------------------------------------------------------

    /**
     * Element::append
     *
     * @param $source
     *
     * @return \DOMNode
     */
    public function append($source)
    {
        $importNode = $this->ownerDocument->importNode($this->ownerDocument->importSourceNode($source), true);

        return $this->appendChild($importNode);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::prepend
     *
     * @param $source
     *
     * @return \DOMNode
     */
    public function prepend($source)
    {
        $importNode = $this->ownerDocument->importNode($this->ownerDocument->importSourceNode($source), true);

        return $this->insertBefore($importNode, $this->firstChild);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::before
     *
     * @param $source
     *
     * @return \DOMNode
     */
    public function before($source)
    {
        $importNode = $this->ownerDocument->importNode($this->ownerDocument->importSourceNode($source), true);

        return $this->parentNode->insertBefore($importNode, $this);
    }

    // ------------------------------------------------------------------------

    /**
     * Element::after
     *
     * @param $source
     *
     * @return bool
     */
    public function after($source)
    {
        $importNode = $this->ownerDocument->importNode($this->ownerDocument->importSourceNode($source), true);

        $isFoundSameNode = false;

        foreach ($this->parentNode->childNodes as $key => $childNode) {
            if ($childNode->isSameNode($this)) {
                $isFoundSameNode = true;
                continue;
            } elseif ($isFoundSameNode) {
                $this->parentNode->insertBefore($importNode, $childNode);

                return true;
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Element::remove
     *
     * @return \DOMNode
     */
    public function remove()
    {
        return $this->parentNode->removeChild($this);
    }
}