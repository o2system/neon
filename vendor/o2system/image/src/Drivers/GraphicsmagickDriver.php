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

namespace O2System\Image\Drivers;

// ------------------------------------------------------------------------

use O2System\Image\Abstracts\AbstractDriver;
use O2System\Image\Abstracts\AbstractWatermark;
use O2System\Image\Dimension;
use O2System\Image\Watermark\Overlay;
use O2System\Image\Watermark\Text;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException;

/**
 * Class GraphicsmagickDriver
 *
 * @package O2System\Image\Drivers
 */
class GraphicsmagickDriver extends AbstractDriver
{
    /**
     * GraphicsmagickDriver::__construct
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException
     */
    public function __construct()
    {
        if ( ! class_exists('Gmagick', false)) {
            throw new BadPhpExtensionCallException('IMAGE_E_PHP_EXTENSION', 0, ['gmagick']);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::__destruct
     */
    public function __destruct()
    {
        if (is_object($this->sourceImageResource)) {
            $this->sourceImageResource->destroy();
        }

        if (is_object($this->resampleImageResource)) {
            $this->resampleImageResource->destroy();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::createFromString
     *
     * Create an image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return bool
     * @throws \GmagickException
     */
    public function createFromString($imageString)
    {
        $this->sourceImageResource = new \Gmagick();

        try {

            $this->sourceImageResource->readimageblob($imageString);

            return true;

        } catch (\GmagickException $e) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return bool
     */
    public function rotate($degrees)
    {
        $resampleImageResource =& $this->getResampleImageResource();

        try {

            $resampleImageResource->rotateimage('#000000', $degrees);

            return true;

        } catch (\GmagickException $e) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return bool
     */
    public function flip($axis)
    {
        $gdAxis = [
            1 => IMG_FLIP_HORIZONTAL,
            2 => IMG_FLIP_VERTICAL,
            3 => IMG_FLIP_BOTH,
        ];

        if (array_key_exists($axis, $gdAxis)) {
            $resampleImageResource =& $this->getResampleImageResource();

            try {
                switch ($axis) {
                    case 1:
                        $resampleImageResource->flopimage();
                        break;
                    case 2:
                        $resampleImageResource->flipimage();
                        break;
                    case 3:
                        $resampleImageResource->flopimage();
                        $resampleImageResource->flipimage();
                        break;
                }

                return true;

            } catch (\GmagickException $e) {

                $this->errors[ $e->getCode() ] = $e->getMessage();

            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::resize
     *
     * Resize an image using the given new width and height.
     *
     * @param bool $crop Perform auto crop or not
     *
     * @return bool
     */
    public function resize($crop = false)
    {
        if ($crop) {
            return $this->resizeCrop();
        } else {
            $sourceDimension = $this->sourceImageFile->getDimension();
            $resampleDimension = $this->resampleImageFile->getDimension();

            if (($sourceDimension->getWidth() <= $resampleDimension->getWidth()) && ($sourceDimension->getHeight() <= $resampleDimension->getHeight())) {
                return true;
            } //no resizing needed

            //try max width first...
            $resizeRatio = $resampleDimension->getWidth() / $sourceDimension->getWidth();
            $resizeWidth = $resampleDimension->getWidth();
            $resizeHeight = $sourceDimension->getHeight() * $resizeRatio;

            //if that didn't work
            if ($resizeHeight > $resampleDimension->getHeight()) {
                $resizeRatio = $resampleDimension->getHeight() / $sourceDimension->getHeight();
                $resizeHeight = $resampleDimension->getHeight();
                $resizeWidth = $sourceDimension->getWidth() * $resizeRatio;
            }

            $resampleImageResource =& $this->getResampleImageResource();

            try {
                $resampleImageResource->resizeimage($resizeWidth, $resizeHeight,
                    \Gmagick::FILTER_CATROM, 0.9, true);

                return true;

            } catch (\GmagickException $e) {

                $this->errors[ $e->getCode() ] = $e->getMessage();

                return false;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::resizeCrop
     *
     * @return bool
     */
    protected function resizeCrop()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        //try max width first...
        $resizeRatio = $resampleDimension->getWidth() / $sourceDimension->getWidth();
        $resizeWidth = $resampleDimension->getWidth();
        $resizeHeight = $sourceDimension->getHeight() * $resizeRatio;

        //if that didn't work
        if ($resizeHeight > $resampleDimension->getHeight()) {
            $resizeRatio = $resampleDimension->getHeight() / $sourceDimension->getHeight();
            $resizeHeight = $resampleDimension->getHeight();
            $resizeWidth = $sourceDimension->getWidth() * $resizeRatio;
        }

        $resampleImageResource =& $this->getResampleImageResource();

        if ($resampleDimension->getOrientation() === 'SQUARE') {

            try {

                $resampleImageResource->resizeimage($resizeWidth, $resizeHeight, \Gmagick::FILTER_LANCZOS, 0.9, false);
                $resampleAxis = new Dimension\Axis(
                    ($resizeWidth - $resampleDimension->getWidth()) / 2,
                    ($resizeHeight - $resampleDimension->getWidth()) / 2
                );

            } catch (\GmagickException $e) {

                $this->errors[ $e->getCode() ] = $e->getMessage();

                return false;

            }
        } else {
            switch ($resampleDimension->getFocus()) {
                default:
                case 'CENTER':
                    $resampleAxis = new Dimension\Axis(
                        ($sourceDimension->getWidth() / 2) - ($resizeWidth / 2),
                        ($sourceDimension->getHeight() / 2) - ($resizeHeight / 2)
                    );
                    break;
                case 'NORTH':
                    $resampleAxis = new Dimension\Axis(
                        ($sourceDimension->getWidth() - $resizeWidth) / 2,
                        0
                    );
                    break;
                case 'NORTHWEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        0
                    );
                    break;
                case 'NORTHEAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        0
                    );
                    break;
                case 'SOUTH':
                    $resampleAxis = new Dimension\Axis(
                        ($sourceDimension->getWidth() - $resizeWidth) / 2,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHWEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHEAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'WEST':
                    $resampleAxis = new Dimension\Axis(
                        0,
                        ($sourceDimension->getHeight() - $resizeHeight) / 2
                    );
                    break;
                case 'EAST':
                    $resampleAxis = new Dimension\Axis(
                        $sourceDimension->getWidth() - $resizeWidth,
                        ($sourceDimension->getHeight() - $resizeHeight) / 2
                    );
                    break;
            }

            try {

                $resampleImageResource->resizeimage($sourceDimension->getWidth(), $sourceDimension->getHeight(),
                    \Gmagick::FILTER_CATROM, 0.9, true);

            } catch (\GmagickException $e) {

                $this->errors[ $e->getCode() ] = $e->getMessage();

            }
        }

        return $this->crop($resampleDimension->withAxis($resampleAxis));
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::crop
     *
     * Crop an image.
     *
     * @param \O2System\Image\Dimension $dimension
     *
     * @return bool
     */
    public function crop(Dimension $dimension)
    {
        $resampleImageResource =& $this->getResampleImageResource();

        try {

            $resampleImageResource->cropimage(
                $dimension->getWidth(),
                $dimension->getHeight(),
                $dimension->getAxis()->getX(),
                $dimension->getAxis()->getY()
            );

            return true;

        } catch (\GmagickException $e) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }

    /**
     * GraphicsmagickDriver::watermark
     *
     * Watermark an image.
     *
     * @param \O2System\Image\Abstracts\AbstractWatermark $watermark
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException
     */
    public function watermark(AbstractWatermark $watermark)
    {
        $resampleImageResource =& $this->getResampleImageResource();

        if ($watermark instanceof Text) {

            $draw = new \GmagickDraw();
            $draw->setFont($watermark->getFontPath());
            $draw->setFontSize($watermark->getFontSize());
            $draw->setFillColor($watermark->getFontColor());

            if (false !== ($textAxis = $watermark->getAxis())) {
                $draw->annotation($textAxis->getX(), $textAxis->getY(), $watermark->getString());
            } else {
                switch ($watermark->getPosition()) {
                    default:
                    case 'MIDDLE_MIDDLE':
                    case 'MIDDLE':
                    case 'CENTER':
                        $draw->setgravity(\Gmagick::GRAVITY_CENTER);
                        break;

                    case 'MIDDLE_LEFT':
                        $draw->setgravity(\Gmagick::GRAVITY_WEST);
                        break;

                    case 'MIDDLE_RIGHT':
                        $draw->setgravity(\Gmagick::GRAVITY_EAST);
                        break;

                    case 'MIDDLE_TOP':
                        $draw->setgravity(\Gmagick::GRAVITY_NORTH);
                        break;

                    case 'MIDDLE_BOTTOM':
                        $draw->setgravity(\Gmagick::GRAVITY_SOUTH);
                        break;

                    case 'TOP_LEFT':
                        $draw->setgravity(\Gmagick::GRAVITY_NORTHWEST);
                        break;

                    case 'TOP_RIGHT':
                        $draw->setgravity(\Gmagick::GRAVITY_NORTHEAST);
                        break;

                    case 'BOTTOM_LEFT':
                        $draw->setgravity(\Gmagick::GRAVITY_SOUTHWEST);
                        break;

                    case 'BOTTOM_RIGHT':
                        $draw->setgravity(\Gmagick::GRAVITY_SOUTHEAST);
                        break;
                }
            }

            try {

                $resampleImageResource->annotateimage(
                    $draw,
                    $watermark->getPadding(),
                    $watermark->getPadding(),
                    $watermark->getAngle(),
                    $watermark->getString()
                );

                return true;

            } catch (\GmagickException $e) {

                $this->errors[ $e->getCode() ] = $e->getMessage();

            }
        } elseif ($watermark instanceof Overlay) {
            $watermarkImage = new self;
            $watermarkImage->setSourceImage($watermark->getImagePath());
            $watermarkImage->createFromSource();

            $watermarkImageFile = $watermarkImage->getSourceImageFile();
            $watermarkImageDimension = $watermarkImageFile->getDimension();
            $watermarkImageDimension->maintainAspectRatio = true;

            $resampleImageDimension = $this->resampleImageFile->getDimension();

            if (false === ($scale = $watermark->getImageScale())) {
                $scale = min(
                    round((($resampleImageDimension->getWidth() / 2) / $watermarkImageDimension->getWidth()) * 100),
                    round((($resampleImageDimension->getHeight() / 2) / $watermarkImageDimension->getHeight()) * 100)
                );
            }

            if ($scale > 0) {
                $watermarkImage->setResampleImage($watermarkImageFile->withDimension(
                    $watermarkImageDimension
                        ->withScale($scale)
                ));
            }

            $watermarkImageDimension = $watermarkImage->getResampleImageFile()->getDimension();

            if ($watermarkImage->scale()) {
                $watermarkImageResource = $watermarkImage->getResampleImageResource();

                if (false !== ($watermarkAxis = $watermark->getAxis())) {
                    $watermarkImageAxisX = $watermarkAxis->getX();
                    $watermarkImageAxisY = $watermarkAxis->getY();
                } else {
                    switch ($watermark->getPosition()) {
                        default:
                        case 'MIDDLE_MIDDLE':
                        case 'MIDDLE':
                        case 'CENTER':
                            $watermarkImageAxisX = ($resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth()) / 2;
                            $watermarkImageAxisY = ($resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight()) / 2;
                            break;

                        case 'MIDDLE_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = ($resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight()) / 2;
                            break;

                        case 'MIDDLE_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ($watermarkImageDimension->getWidth() + $watermark->getPadding());
                            $watermarkImageAxisY = ($resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight()) / 2;
                            break;

                        case 'MIDDLE_TOP':
                            $watermarkImageAxisX = ($resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth()) / 2;
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'MIDDLE_BOTTOM':
                            $watermarkImageAxisX = ($resampleImageDimension->getWidth() - $watermarkImageDimension->getWidth()) / 2;
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - ($watermarkImageDimension->getHeight() + $watermark->getPadding());
                            break;

                        case 'TOP_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'TOP_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ($watermarkImageDimension->getWidth() + $watermark->getPadding());
                            $watermarkImageAxisY = $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'BOTTOM_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - $watermarkImageDimension->getHeight() + $watermark->getPadding();
                            break;

                        case 'BOTTOM_RIGHT':
                            $watermarkImageAxisX = $resampleImageDimension->getWidth() - ($watermarkImageDimension->getWidth() + $watermark->getPadding());
                            $watermarkImageAxisY = $resampleImageDimension->getHeight() - ($watermarkImageDimension->getHeight() + $watermark->getPadding());
                            break;
                    }
                }

                try {
                    $resampleImageResource->compositeimage($watermarkImageResource, \Gmagick::COMPOSITE_OVER,
                        $watermarkImageAxisX,
                        $watermarkImageAxisY);

                    return true;
                } catch (\GmagickException $e) {
                    $this->errors[ $e->getCode() ] = $e->getMessage();
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return static
     */
    public function createFromSource()
    {
        $this->sourceImageResource = new \Gmagick($this->sourceImageFile->getRealPath());
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    public function scale()
    {
        $resampleDimension = $this->resampleImageFile->getDimension();
        $resampleImageResource =& $this->getResampleImageResource();

        try {

            $resampleImageResource->scaleimage(
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                true
            );

            return true;

        } catch (\GmagickException $e) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::display
     *
     * Display an image.
     *
     * @return void
     */
    public function display($quality = 100, $mime = null)
    {
        $filename = pathinfo($this->sourceImageFile->getBasename(), PATHINFO_FILENAME);
        $extension = pathinfo($this->sourceImageFile->getBasename(), PATHINFO_EXTENSION);

        if (empty($mime)) {
            $mime = $this->sourceImageFile->getMime();
            $mime = is_array($mime) ? $mime[ 0 ] : $mime;

            $extension = $this->getMimeExtension($mime);
        }

        header('Content-Disposition: filename=' . $filename . '.' . $extension);
        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Content-Type: ' . $mime);

        $blob = $this->blob($quality, $mime);

        header('ETag: ' . md5($blob));

        echo $blob;

        exit(0);
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::blob
     *
     * Returns image string blob.
     *
     * @return string
     */
    public function blob($quality = 100, $mime = null)
    {
        $imageBlob = '';

        $filename = pathinfo($this->sourceImageFile->getBasename(), PATHINFO_FILENAME);
        $extension = pathinfo($this->sourceImageFile->getBasename(), PATHINFO_EXTENSION);

        if (empty($mime)) {
            $mime = $this->sourceImageFile->getMime();
            $mime = is_array($mime) ? $mime[ 0 ] : $mime;

            $extension = $this->getMimeExtension($mime);
        }

        if ($this->save($tempImageFilePath = rtrim(sys_get_temp_dir(),
                DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename . '.' . $extension,
            $quality)
        ) {
            $imageBlob = readfile($tempImageFilePath);
            unlink($tempImageFilePath);
        }

        return $imageBlob;
    }

    // ------------------------------------------------------------------------

    /**
     * GraphicsmagickDriver::save
     *
     * Save an image.
     *
     * @param string $imageTargetFilePath
     * @param int    $quality
     *
     * @return bool
     */
    public function save($imageTargetFilePath, $quality = 100)
    {
        $resampleImageResource =& $this->getResampleImageResource();
        $resampleImageResource->setCompressionQuality($quality);
        $resampleImageResource->stripimage();

        $extension = pathinfo($imageTargetFilePath, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $resampleImageResource->setImageFormat('jpg');
                break;

            case 'gif':
                $resampleImageResource->setImageFormat('gif');
                break;

            case 'png':
                $resampleImageResource->setImageFormat('png');
                $resampleImageResource->setImageAlphaChannel(\Gmagick::ALPHACHANNEL_ACTIVATE);
                $resampleImageResource->setBackgroundColor(new \GmagickPixel('transparent'));
                break;

            case 'webp':
                $resampleImageResource->setImageFormat('webp');
                $resampleImageResource->setImageAlphaChannel(\Gmagick::ALPHACHANNEL_ACTIVATE);
                $resampleImageResource->setBackgroundColor(new \GmagickPixel('transparent'));
                break;
        }

        try {

            $resampleImageResource->writeimage($imageTargetFilePath);

            return true;

        } catch (\GmagickException $e) {

            $this->errors[ $e->getCode() ] = $e->getMessage();

        }

        return false;
    }
}