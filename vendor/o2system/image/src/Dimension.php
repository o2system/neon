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

namespace O2System\Image;

// ------------------------------------------------------------------------

use O2System\Image\Dimension\Axis;

/**
 * Class Dimension
 *
 * Image dimension object class.
 *
 * @package O2System\Image
 */
class Dimension
{
    /**
     * Dimension::$maintainAspectRatio
     *
     * Maintain image dimension aspect ratio.
     *
     * @var bool
     */
    public $maintainAspectRatio = true;

    /**
     * Dimension::$directive
     *
     * Image dimension directive.
     *
     * @var string
     */
    protected $directive = 'RATIO';

    /**
     * Dimension::$orientation
     *
     * Image dimension orientation.
     *
     * @var string
     */
    protected $orientation = 'AUTO';

    /**
     * Dimension::$quadrant
     *
     * Image dimension quadrant.
     *
     * @var string
     */
    protected $quadrant = 'CENTER';

    /**
     * Dimension::$width
     *
     * Image width dimension.
     *
     * @var int
     */
    protected $width = 0;

    /**
     * Dimension::$height
     *
     * Image height dimension.
     *
     * @var int
     */
    protected $height = 0;

    /**
     * Dimension::$axis
     *
     * Image dimension axis.
     *
     * @var Axis
     */
    protected $axis;

    // ------------------------------------------------------------------------

    /**
     * Dimension::__construct
     *
     * @param int $width  Image width.
     * @param int $height Image height.
     */
    public function __construct($width, $height, $x = 0, $y = 0)
    {
        $this->width = (int)$width;
        $this->height = (int)$height;

        $this->axis = new Axis($x, $y);
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getOrientation
     *
     * @return string
     */
    public function getOrientation()
    {
        if ($this->orientation === 'AUTO') {
            if ($this->width > $this->height) {
                $this->orientation = 'LANDSCAPE';
            } elseif ($this->width < $this->height) {
                $this->orientation = 'PORTRAIT';
            } elseif ($this->width == $this->height) {
                $this->orientation = 'SQUARE';
            }
        }

        return $this->orientation;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withOrientation
     *
     * Get new image dimension with defined orientation.
     *
     * @param string $orientation Supported image orientation:
     *                            1. AUTO
     *                            2. LANDSCAPE
     *                            3. PORTRAIT
     *                            4. SQUARE
     *
     * @return Dimension
     */
    public function withOrientation($orientation)
    {
        $newDimension = clone $this;

        if (in_array($orientation, ['AUTO', 'LANDSCAPE', 'PORTRAIT', 'SQUARE'])) {
            $newDimension->orientation = $orientation;
        }

        return $newDimension;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withDirective
     *
     * Get new image dimension with defined directive.
     *
     * return Dimension
     */
    public function withDirective($directive)
    {
        $newDimension = clone $this;

        if (in_array($directive, ['RATIO', 'UP', 'DOWN'])) {
            $newDimension->directive = $directive;
        }

        return $newDimension;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getFocus
     *
     * Gets defined dimension focus.
     *
     * @return string
     */
    public function getFocus()
    {
        return $this->quadrant;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withFocus
     *
     * Get new image dimension with defined focus.
     *
     * +----+----+----+
     * | NW | N  | NE |
     * +----+----+----+
     * | W  | C  | E  |
     * +----+----+----+
     * | SW | S  | SE |
     * +----+----+----+
     *
     * @param string $focus    Supported image quadrant:
     *                         1. CENTER
     *                         2. NORTH
     *                         3. NORTHWEST
     *                         4. NORTHEAST
     *                         5. SOUTH
     *                         6. SOUTHWEST
     *                         7. SOUTHEAST
     *                         8. WEST
     *                         9. EAST
     *
     * @return Dimension
     */
    public function withFocus($focus)
    {
        $newDimension = clone $this;

        if (in_array($focus,
            ['CENTER', 'NORTH', 'NORTHWEST', 'NORTHEAST', 'SOUTH', 'SOUTHWEST', 'SOUTHEAST', 'EAST', 'WEST'])) {
            $newDimension->quadrant = $focus;
        }

        return $newDimension;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getAxis
     *
     * Gets image dimension axis.
     *
     * @return \O2System\Image\Dimension\Axis
     */
    public function getAxis()
    {
        return $this->axis;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withAxis
     *
     * Gets new image file with new sets of axis.
     *
     * @param Axis $axis New image axis.
     *
     * @return Dimension
     */
    public function withAxis(Axis $axis)
    {
        $newDimension = clone $this;
        $newDimension->axis = $axis;

        return $newDimension;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getRatio
     *
     * Gets image dimension ratio.
     *
     * @return int
     */
    public function getRatio()
    {
        return (int)round($this->width / $this->height);
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withWidth
     *
     * Gets new image dimension with defined width.
     *
     * @param int $newWidth New image width.
     *
     * @return \O2System\Image\Dimension
     */
    public function withWidth($newWidth)
    {
        return $this->withSize($newWidth, ceil($newWidth * $this->height / $this->width));
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withSize
     *
     * Gets new image dimension with defined width and height.
     *
     * @param int $newWidth  New image width.
     * @param int $newHeight New image height.
     *
     * @return \O2System\Image\Dimension
     */
    public function withSize($newWidth, $newHeight)
    {
        $newDimension = clone $this;

        if ($newWidth == 0 && $newHeight > 0) {
            $newDimension->withHeight($newHeight);
            $newWidth = $newDimension->getWidth();
        } else {
            if ($newWidth > 0 && $newHeight == 0) {
                $newDimension->withWidth($newWidth);
                $newHeight = $newDimension->getHeight();
            }
        }

        $targetDimension = clone $this;

        if ($this->directive === 'UP') {
            if ($newWidth > $this->width) {
                $targetDimension->width = $newWidth;
                $targetDimension->height = $newHeight;

                return $targetDimension;
            } elseif ($newWidth < $this->width) {
                return $targetDimension;
            }
        } elseif ($this->directive === 'DOWN') {
            if ($newWidth > $this->width) {
                return $targetDimension;
            } elseif ($newWidth < $this->width) {
                $targetDimension->width = $newWidth;
                $targetDimension->height = $newHeight;

                return $targetDimension;
            }
        }

        if ($this->maintainAspectRatio) {
            $targetDimension->width = $newWidth > $this->width ? $this->width : $newWidth;
            $targetDimension->height = $newHeight > $this->height ? $this->height : $newHeight;
        } else {
            $targetDimension->width = (int)$newWidth;
            $targetDimension->height = (int)$newHeight;
        }

        return $targetDimension;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withHeight
     *
     * Gets new image dimension with defined height.
     *
     * @param int $newHeight New image height.
     *
     * @return \O2System\Image\Dimension
     */
    public function withHeight($newHeight)
    {
        return $this->withSize(ceil($this->width * $newHeight / $this->height), $newHeight);
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getWidth
     *
     * Gets image dimension width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::getHeight
     *
     * Gets image dimension height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    // ------------------------------------------------------------------------

    /**
     * Dimension::withScale
     *
     * Gets new image dimension with defined scale.
     *
     * @param int $scale New image scale.
     *
     * @return \O2System\Image\Dimension
     */
    public function withScale($scale)
    {
        $newDimension = clone $this;

        return $newDimension->withSize(
            round($this->width * ($scale / 100)),
            round($this->height * ($scale / 100))
        );
    }
}