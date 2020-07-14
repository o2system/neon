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

namespace O2System\Image\Abstracts;

// ------------------------------------------------------------------------

use O2System\Image\Dimension\Axis;

/**
 * Class AbstractWatermark
 *
 * @package O2System\Image\Abstracts
 */
abstract class AbstractWatermark
{
    /**
     * AbstractWatermark::$position
     *
     * Watermark position.
     *
     * @var string
     */
    protected $position = 'AUTO';

    /**
     * AbstractWatermark::$axis
     *
     * Watermark axis.
     *
     * @var Axis
     */
    protected $axis;

    /**
     * AbstractWatermark::$padding
     *
     * Watermark padding.
     *
     * @var int
     */
    protected $padding = 25;

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::getPosition
     *
     * Gets watermark position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::setPosition
     *
     * Sets watermark position.
     *
     * @param string $position
     *
     * @return static
     */
    public function setPosition($position)
    {
        if (in_array($position, [
            'CENTER',
            'MIDDLE',
            'MIDDLE_MIDDLE',
            'MIDDLE_LEFT',
            'MIDDLE_RIGHT',
            'MIDDLE_TOP',
            'MIDDLE_BOTTOM',
            'TOP_LEFT',
            'TOP_RIGHT',
            'BOTTOM_LEFT',
            'BOTTOM_RIGHT',
        ])) {
            $this->position = strtoupper($position);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::getAxis
     *
     * Gets watermark axis.
     *
     * @return bool|\O2System\Image\Dimension\Axis
     */
    public function getAxis()
    {
        if ($this->axis instanceof Axis) {
            return $this->axis;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::setAxis
     *
     * Sets watermark axis.
     *
     * @param Axis $axis
     *
     * @return static
     */
    public function setAxis(Axis $axis)
    {
        $this->axis = $axis;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::getPadding
     *
     * Gets watermark padding.
     *
     * @return int
     */
    public function getPadding()
    {
        return $this->padding;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractWatermark::setPadding
     *
     * Sets watermark padding.
     *
     * @param int $padding
     *
     * @return static
     */
    public function setPadding($padding)
    {
        $this->padding = (int)$padding;

        return $this;
    }
}