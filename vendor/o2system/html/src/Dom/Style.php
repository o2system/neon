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
 * Class Style
 *
 * @package O2System\HTML\DOM
 */
class Style extends \ArrayIterator
{
    /**
     * Style::import
     *
     * @param \O2System\Html\Dom\Style $style
     */
    public function import(Style $style)
    {
        foreach ($style->getArrayCopy() as $styleTextContent) {
            $this->append($styleTextContent);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Style::offsetSet
     *
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $value = trim($value);

        if ( ! empty($value)) {
            parent::offsetSet($offset, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Style::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return PHP_EOL . implode(PHP_EOL, $this->getArrayCopy()) . PHP_EOL;
    }
}