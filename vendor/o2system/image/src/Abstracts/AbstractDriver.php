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

use O2System\Image\File;

/**
 * Class AbstractDriver
 *
 * @package O2System\Image\Abstracts
 */
abstract class AbstractDriver
{
    /**
     * AbstractDriver::$imageProperties
     *
     * Library image file info.
     *
     * @var File
     */
    protected $sourceImageFile;

    /**
     * AbstractDriver::$imageResource
     *
     * Library image resource.
     *
     * @var resource|\Imagick|\Gmagick
     */
    protected $sourceImageResource;

    /**
     * AbstractDriver::$imageResample
     *
     * Library image resample resource.
     *
     * @var resource|\Imagick|\Gmagick
     */
    protected $resampleImageResource;

    /**
     * AbstractDriver::$imageResampleProperties
     *
     * Library image resample resource.
     *
     * @var File
     */
    protected $resampleImageFile;

    /**
     * AbstractDriver::$errors
     *
     * Library errors list.
     *
     * @var array
     */
    protected $errors = [];

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::setSourceImage
     *
     * Sets image source file path.
     *
     * @param string $imageFilePath Image source file path.
     *
     * @return bool
     */
    public function setSourceImage($imageFilePath)
    {
        if (is_file($imageFilePath)) {
            $this->sourceImageFile = new File($imageFilePath);
            $this->resampleImageFile = $this->sourceImageFile;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::setResampleImage
     *
     * Sets resample image file.
     *
     * @param File $resampleFile Resample image file object.
     *
     * @return static
     */
    public function setResampleImage(File $resampleFile)
    {
        $this->resampleImageFile = $resampleFile;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getSourceImageFile
     *
     * Gets image resource file object.
     *
     * @return \O2System\Image\File
     */
    public function getSourceImageFile()
    {
        return $this->sourceImageFile;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getResampleImageFile
     *
     * Gets image resample file object.
     *
     * @return \O2System\Image\File
     */
    public function getResampleImageFile()
    {
        return $this->resampleImageFile;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::hasErrors
     *
     * Gets library image errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        if (count($this->errors)) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getErrors
     *
     * Gets library image errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getTotalErrors
     *
     * Gets library image errors.
     *
     * @return bool
     */
    public function getTotalErrors()
    {
        return count($this->errors);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getMimeExtension
     *
     * Gets image mime extension.
     *
     * @param string $mime
     *
     * @return bool|string
     */
    public function getMimeExtension($mime)
    {
        $extensions = [
            'image/gif'  => 'gif',
            'image/jpg'  => 'jpg',
            'image/jpeg' => 'jpeg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (array_key_exists($mime, $extensions)) {
            return $extensions[ $mime ];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return static
     */
    abstract public function createFromSource();

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::createFromString
     *
     * Create an image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return void
     */
    abstract public function createFromString($imageString);

    /**
     * AbstractDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return void
     */
    abstract public function rotate($degrees);

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::resize
     *
     * Resize an image using the given new width and height.
     *
     * @return bool
     */
    abstract public function resize();

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    abstract public function scale();

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return void
     */
    abstract public function flip($axis);

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::display
     *
     * Display an image.
     *
     * @return void
     */
    abstract public function display($quality = 100, $mime = null);

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::blob
     *
     * Return an image as blob string.
     *
     * @return string
     */
    abstract public function blob($quality = 100, $mime = null);

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::save
     *
     * Save an image.
     *
     * @param string $imageTargetFilePath
     * @param int    $quality
     *
     * @return bool
     */
    abstract public function save($imageTargetFilePath, $quality = 100);

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getImageResource
     *
     * Gets library image resource.
     *
     * @return resource
     */
    protected function &getSourceImageResource()
    {
        return $this->sourceImageResource;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractDriver::getImageOnProcess
     *
     * Gets library image on process resource.
     *
     * @return resource
     */
    protected function &getResampleImageResource()
    {
        if ( ! is_resource($this->resampleImageResource)) {
            $this->resampleImageResource = $this->sourceImageResource;
        }

        return $this->resampleImageResource;
    }
}