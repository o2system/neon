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
 * Class Text
 *
 * @package O2System\Image\Watermark
 */
class Text extends AbstractWatermark
{
    /**
     * Text::$fontTruetype
     *
     * Text use truetype font flag.
     *
     * @var bool
     */
    protected $fontTruetype = true;

    /**
     * Text::$fontPath
     *
     * Text font path.
     *
     * @var string
     */
    protected $fontPath;

    /**
     * Text::$fontSize
     *
     * Text font size.
     *
     * @var int
     */
    protected $fontSize;

    /**
     * Text::$fontColor
     *
     * Text font color.
     *
     * @var string
     */
    protected $fontColor = 'ffffff';

    /**
     * Text::$string
     *
     * Text string content.
     *
     * @var string
     */
    protected $string;

    /**
     * Text::$angle
     *
     * Text angle.
     *
     * @var int
     */
    protected $angle = 0;

    // ------------------------------------------------------------------------

    /**
     * Text::signature
     *
     * Create a signature text image watermark with Jellyka Saint-Andrew's Queen Truetype Font.
     *
     * @param string $string
     * @param int    $size
     * @param string $color
     *
     * @return static
     */
    public function signature($string, $size = 25, $color = 'ffffff')
    {
        $this->setFontPath(__DIR__ . DIRECTORY_SEPARATOR . 'Fonts/Jellyka_Saint-Andrew\'s_Queen.ttf')
            ->setFontSize($size)
            ->setFontColor($color)
            ->setString($string);

        if ($this->position === 'AUTO') {
            $this->position = 'CENTER';
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::copyright
     *
     * Create a copyright text image watermark with Express Way Regular Truetype Font.
     *
     * @param string $string
     * @param int    $size
     * @param string $color
     *
     * @return $this
     */
    public function copyright($string, $size = 10, $color = 'ffffff')
    {
        $this->setFontPath(__DIR__ . DIRECTORY_SEPARATOR . 'Fonts/ExpresswayRg-Regular.ttf')
            ->setFontSize($size)
            ->setFontColor($color)
            ->setString($string);

        if ($this->position === 'AUTO') {
            $this->position = 'BOTTOM_LEFT';
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::getFontPath
     *
     * Gets text font path.
     *
     * @return string
     */
    public function getFontPath()
    {
        return $this->fontPath;
    }

    // -------------------------------------------------------------------------

    /**
     * Text::setFontPath
     *
     * Sets text font path.
     *
     * @param $fontPath
     *
     * @return $this
     */
    public function setFontPath($fontPath)
    {
        if (is_file($fontPath)) {
            $this->fontTruetype = true;
            $this->fontPath = $fontPath;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::getFontSize
     *
     * Gets text font size.
     *
     * @return int
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    // ------------------------------------------------------------------------

    public function setFontSize($fontSize)
    {
        $this->fontSize = (int)$fontSize;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::getFontColor
     *
     * Gets text font color.
     *
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    // ------------------------------------------------------------------------

    public function setFontColor($fontColor)
    {
        $this->fontColor = '#' . ltrim($fontColor, '#');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::getString
     *
     * Gets text string.
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::setString
     *
     * @param $string
     *
     * @return static
     */
    public function setString($string)
    {
        $this->string = trim($string);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Text::getAngle
     *
     * Gets text angle.
     *
     * @return int
     */
    public function getAngle()
    {
        return $this->angle;
    }
}