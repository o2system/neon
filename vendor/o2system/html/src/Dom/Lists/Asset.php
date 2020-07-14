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

use O2System\Html\Document;
use O2System\Html\Dom\Element;

/**
 * Class Asset
 *
 * @package O2System\HTML\DOM\Lists
 */
class Asset extends \ArrayIterator
{
    /**
     * Asset::$element
     *
     * @var string
     */
    public $element = 'link';

    /**
     * Asset::$ownerDocument
     *
     * @var \O2System\Html\Document
     */
    public $ownerDocument;

    // ------------------------------------------------------------------------

    /**
     * Asset::__construct
     *
     * @param \O2System\Html\Document $ownerDocument
     */
    public function __construct(Document $ownerDocument)
    {
        $this->ownerDocument =& $ownerDocument;
    }

    // ------------------------------------------------------------------------

    /**
     * Asset::import
     *
     * @param \O2System\Html\Dom\Lists\Asset $assetNodes
     *
     * @return static
     */
    public function import(Asset $assetNodes)
    {
        if (is_array($assetNodes = $assetNodes->getArrayCopy())) {
            foreach ($assetNodes as $name => $value) {
                $this->offsetSet($name, $value);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Asset::offsetSet
     *
     * @param string $name
     * @param string $value
     */
    public function offsetSet($name, $value)
    {
        if ($value instanceof Element) {
            parent::offsetSet($name, $value);
        } else {
            $meta = $this->ownerDocument->createElement($this->element);
            $meta->setAttribute($name, $value);

            parent::offsetSet($name, $meta);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Asset::createElement
     *
     * @param array $attributes
     *
     * @return \DOMElement
     */
    public function createElement(array $attributes)
    {
        $element = $this->ownerDocument->createElement($this->element);

        $name = null;
        foreach ($attributes as $key => $value) {
            $element->setAttribute($key, $value);
        }

        $this[] = $element;

        return $element;
    }
}