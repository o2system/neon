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

namespace O2System\Image\Watermark;

// ------------------------------------------------------------------------

use O2System\Image\Abstracts\AbstractWatermark;

/**
 * Class Overlay
 *
 * @package O2System\Image\Watermark
 */
class Overlay extends AbstractWatermark
{
    /**
     * Overlay::$imagePath
     *
     * Overlay image path.
     *
     * @var string
     */
    protected $imagePath;

    /**
     * Overlay::$imageScale
     *
     * Overlay image scale.
     *
     * @var int
     */
    protected $imageScale;

    // ------------------------------------------------------------------------

    /**
     * Overlay::getImagePath
     *
     * Gets overlay image path.
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    // ------------------------------------------------------------------------

    /**
     * Overlay::setImagePath
     *
     * Sets overlay image path.
     *
     * @param string $imagePath
     *
     * @return static
     */
    public function setImagePath($imagePath)
    {
        if (is_file($imagePath)) {
            $this->imagePath = realpath($imagePath);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Overlay::getImageScale
     *
     * Gets overlay image scale.
     *
     * @return int|bool
     */
    public function getImageScale()
    {
        if ( ! is_null($this->imageScale)) {
            return $this->imageScale;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Overlay::setImageScale
     *
     * Sets overlay image scale.
     *
     * @param int $scale
     *
     * @return $this
     */
    public function setImageScale($scale)
    {
        $this->imageScale = (int)$scale;

        return $this;
    }
}