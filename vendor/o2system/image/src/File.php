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

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class File
 *
 * @package O2System\Image\DataStructures
 */
class File extends \O2System\Filesystem\File
{
    /**
     * File::$dimension
     *
     * Image file dimension.
     *
     * @var Dimension
     */
    private $dimension;

    /**
     * File::$bits
     *
     * Image bits.
     *
     * @var int
     */
    private $bits;

    /**
     * File::$channels
     *
     * Image channels.
     *
     * @var int
     */
    private $channels;

    /**
     * File::$type
     *
     * Image type.
     *
     * @var int
     */
    private $type;

    // ------------------------------------------------------------------------

    /**
     * File::__construct
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        parent::__construct($filePath);

        // Set image file properties
        if (false !== ($imageSize = getimagesize($filePath))) {
            $this->dimension = new Dimension($imageSize[ 0 ], $imageSize[ 1 ]);
            $this->bits = $imageSize[ 'bits' ];
            $this->channels = isset($imageSize[ 'channels' ]) ? $imageSize[ 'channels' ] : 0;
            $this->type = $imageSize[ 2 ];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * File::getSize
     *
     * Gets image size.
     *
     * @return Dimension
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    // ------------------------------------------------------------------------

    /**
     * File::withDimension
     *
     * Gets new image file with new sets of dimension.
     *
     * @param Dimension $dimension New image dimension.
     *
     * @return \O2System\Image\File
     */
    public function withDimension(Dimension $dimension)
    {
        $newFile = clone $this;
        $newFile->dimension = $dimension;

        return $newFile;
    }

    // ------------------------------------------------------------------------

    /**
     * File::getBits
     *
     * Gets image bits.
     *
     * @return int
     */
    public function getBits()
    {
        return $this->bits;
    }

    // ------------------------------------------------------------------------

    /**
     * File::getChannels
     *
     * Gets image channels.
     *
     * @return int
     */
    public function getChannels()
    {
        return $this->channels;
    }

    // ------------------------------------------------------------------------

    /**
     * File::getType
     *
     * Gets image type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    // ------------------------------------------------------------------------

    /**
     * File::getExif
     *
     * Gets image exif.
     *
     * @return SplArrayObject|bool
     */
    public function getExif()
    {
        if (false !== ($exifData = exif_read_data($this->getRealPath()))) {
            return new SplArrayObject($exifData);
        }

        return false;
    }
}