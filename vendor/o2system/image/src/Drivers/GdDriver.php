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

/**
 * Class GdDriver
 *
 * @package O2System\Image\Drivers
 */
class GdDriver extends AbstractDriver
{
    /**
     * GdDriver::__destruct
     */
    public function __destruct()
    {
        if (is_resource($this->sourceImageResource)) {
            @imagedestroy($this->sourceImageResource);
        }

        if (is_resource($this->resampleImageResource)) {
            @imagedestroy($this->resampleImageResource);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::createFromString
     *
     * Create image resource from image string.
     *
     * @param string $imageString Image string.
     *
     * @return void
     */
    public function createFromString($imageString)
    {
        $this->sourceImageResource = imagecreatefromstring($imageString);
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::rotate
     *
     * Rotate an image with a given angle.
     *
     * @param float $degrees Image rotation degrees.
     *
     * @return void
     */
    public function rotate($degrees)
    {
        $resampleImageResource =& $this->getResampleImageResource();

        // Set the background color
        // This won't work with transparent PNG files so we are
        // going to have to figure out how to determine the color
        // of the alpha channel in a future release.
        $alphaChannel = imagecolorallocate($resampleImageResource, 255, 255, 255);

        // Rotate it!
        $this->resampleImageResource = imagerotate($resampleImageResource, $degrees, $alphaChannel);
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::flip
     *
     * Flip an image with a given axis.
     *
     * @param int $axis Flip axis.
     *
     * @return void
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
            imageflip($resampleImageResource, $axis);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::resize
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

            if (function_exists('imagecreatetruecolor')) {
                $this->resampleImageResource = imagecreatetruecolor($resizeWidth, $resizeHeight);

                imagealphablending($this->resampleImageResource, false);
                imagesavealpha($this->resampleImageResource, true);

                $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
                imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                    $resampleDimension->getHeight(), $transparent);

                return imagecopyresampled(
                    $this->resampleImageResource,
                    $this->sourceImageResource,
                    0,
                    0,
                    0,
                    0,
                    $resizeWidth,
                    $resizeHeight,
                    $sourceDimension->getWidth(),
                    $sourceDimension->getHeight()
                );
            } else {
                $this->resampleImageResource = imagecreate($resampleDimension->getWidth(),
                    $resampleDimension->getHeight());

                imagealphablending($this->resampleImageResource, false);
                imagesavealpha($this->resampleImageResource, true);

                $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
                imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                    $resampleDimension->getHeight(), $transparent);

                return imagecopyresampled(
                    $this->resampleImageResource,
                    $this->sourceImageResource,
                    0,
                    0,
                    0,
                    0,
                    $resizeWidth,
                    $resizeHeight,
                    $sourceDimension->getWidth(),
                    $sourceDimension->getHeight()
                );
            }
        }
    }

    // ------------------------------------------------------------------------

    public function resizeCrop()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        if ($resampleDimension->getOrientation() === 'SQUARE') {
            if ($sourceDimension->getOrientation() === 'LANDSCAPE') {
                $sourceSquareSize = $sourceDimension->getHeight();

                $sourceDimension = new Dimension(
                    $sourceSquareSize,
                    $sourceSquareSize,
                    ($sourceDimension->getWidth() - $sourceDimension->getHeight()) / 2,
                    0
                );
            } elseif ($sourceDimension->getOrientation() === 'PORTRAIT') {
                $sourceSquareSize = $sourceDimension->getWidth();

                $sourceDimension = new Dimension(
                    $sourceSquareSize,
                    $sourceSquareSize,
                    0,
                    ($sourceDimension->getHeight() - $sourceDimension->getWidth()) / 2
                );
            }
        } else {

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

            switch ($resampleDimension->getFocus()) {
                default:
                case 'CENTER':

                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ($sourceDimension->getWidth() / 2) - ($resizeWidth / 2),
                        ($sourceDimension->getHeight() / 2) - ($resizeHeight / 2)
                    );

                    break;
                case 'NORTH':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ($sourceDimension->getWidth() - $resizeWidth) / 2,
                        0
                    );
                    break;
                case 'NORTHWEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        0
                    );
                    break;
                case 'NORTHEAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        0
                    );
                    break;
                case 'SOUTH':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        ($sourceDimension->getWidth() - $resizeWidth) / 2,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHWEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'SOUTHEAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        $sourceDimension->getHeight() - $resizeHeight
                    );
                    break;
                case 'WEST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        0,
                        ($sourceDimension->getHeight() - $resizeHeight) / 2
                    );
                    break;
                case 'EAST':
                    $sourceDimension = new Dimension(
                        $resizeWidth,
                        $resizeHeight,
                        $sourceDimension->getWidth() - $resizeWidth,
                        ($sourceDimension->getHeight() - $resizeHeight) / 2
                    );
                    break;
            }
        }

        $resampleAxis = $this->resampleImageFile->getDimension()->getAxis();
        $sourceAxis = $sourceDimension->getAxis();

        if (function_exists('imagecreatetruecolor')) {
            $this->resampleImageResource = imagecreatetruecolor(
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight()
            );

            imagealphablending($this->resampleImageResource, false);
            imagesavealpha($this->resampleImageResource, true);

            $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
            imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                $resampleDimension->getHeight(), $transparent);

            return imagecopyresampled(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        } else {
            $this->resampleImageResource = imagecreate($resampleDimension->getWidth(),
                $resampleDimension->getHeight());

            imagealphablending($this->resampleImageResource, false);
            imagesavealpha($this->resampleImageResource, true);

            $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
            imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                $resampleDimension->getHeight(), $transparent);

            return imagecopyresized(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::crop
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

        if (false !== ($resampleCropImage = imagecrop($resampleImageResource, [
                'x'      => $dimension->getAxis()->getX(),
                'y'      => $dimension->getAxis()->getY(),
                'width'  => $dimension->getWidth(),
                'height' => $dimension->getHeight(),
            ]))
        ) {
            $resampleImageResource = $resampleCropImage;

            return true;
        }

        return false;
    }

    /**
     * GdDriver::watermark
     *
     * Watermark an image.
     *
     * @param \O2System\Image\Abstracts\AbstractWatermark $watermark
     *
     * @return bool
     */
    public function watermark(AbstractWatermark $watermark)
    {
        $resampleImageResource =& $this->getResampleImageResource();

        if ($watermark instanceof Text) {
            $textBox = imagettfbbox($watermark->getFontSize(), $watermark->getAngle(), $watermark->getFontPath(),
                $watermark->getString());

            if ($textBox[ 0 ] < 0 and $textBox[ 6 ]) {
                $textBox[ 1 ] += $textBox[ 0 ];
                $textBox[ 3 ] += $textBox[ 0 ];
                $textBox[ 5 ] += $textBox[ 0 ];
                $textBox[ 7 ] += $textBox[ 0 ];
            }

            $textBox = array_map('abs', $textBox);

            $watermarkImageWidth = max($textBox[ 0 ], $textBox[ 2 ], $textBox[ 4 ], $textBox[ 6 ]);
            $watermarkImageHeight = max($textBox[ 1 ], $textBox[ 3 ], $textBox[ 5 ], $textBox[ 7 ]);

            if (false !== ($watermarkAxis = $watermark->getAxis())) {
                $watermarkImageAxisX = $watermarkAxis->getX();
                $watermarkImageAxisY = $watermarkAxis->getY();
            } else {
                switch ($watermark->getPosition()) {
                    default:
                    case 'MIDDLE_MIDDLE':
                    case 'MIDDLE':
                    case 'CENTER':
                        $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                        $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                        break;

                    case 'MIDDLE_LEFT':
                        $watermarkImageAxisX = $watermark->getPadding();
                        $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                        break;

                    case 'MIDDLE_RIGHT':
                        $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                        $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                        break;

                    case 'MIDDLE_TOP':
                        $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                        $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                        break;

                    case 'MIDDLE_BOTTOM':
                        $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                        $watermarkImageAxisY = imagesy($resampleImageResource) - ($watermarkImageHeight + $watermark->getPadding());
                        break;

                    case 'TOP_LEFT':
                        $watermarkImageAxisX = $watermark->getPadding();
                        $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                        break;

                    case 'TOP_RIGHT':
                        $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                        $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                        break;

                    case 'BOTTOM_LEFT':
                        $watermarkImageAxisX = $watermark->getPadding();
                        $watermarkImageAxisY = imagesy($resampleImageResource) - $watermarkImageHeight + $watermark->getPadding();
                        break;

                    case 'BOTTOM_RIGHT':
                        $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                        $watermarkImageAxisY = imagesy($resampleImageResource) - ($watermarkImageHeight + $watermark->getPadding());
                        break;
                }
            }

            /* Set RGB values for text
             *
             * First character is #, so we don't really need it.
             * Get the rest of the string and split it into 2-length
             * hex values:
             */
            $textColor = str_split(substr($watermark->getFontColor(), 1, 6), 2);
            $textColor = imagecolorclosest($resampleImageResource, hexdec($textColor[ 0 ]),
                hexdec($textColor[ 1 ]),
                hexdec($textColor[ 2 ]));

            imagettftext(
                $resampleImageResource,
                $watermark->getFontSize(),
                $watermark->getAngle(),
                $watermarkImageAxisX,
                $watermarkImageAxisY,
                $textColor,
                $watermark->getFontPath(),
                $watermark->getString()
            );

            return true;
        } elseif ($watermark instanceof Overlay) {

            $watermarkImage = new self;
            $watermarkImage->setSourceImage($watermark->getImagePath());
            $watermarkImage->createFromSource();

            $watermarkImageFile = $watermarkImage->getSourceImageFile();
            $watermarkImageDimension = $watermarkImageFile->getDimension();
            $watermarkImageDimension->maintainAspectRatio = true;

            if (false === ($scale = $watermark->getImageScale())) {
                $scale = min(
                    round(((imagesx($resampleImageResource) / 2) / $watermarkImageDimension->getWidth()) * 100),
                    round(((imagesy($resampleImageResource) / 2) / $watermarkImageDimension->getHeight()) * 100)
                );
            }

            if ($scale > 0) {
                $watermarkImage->setResampleImage($watermarkImageFile->withDimension(
                    $watermarkImageDimension
                        ->withScale($scale)
                ));
            }

            if ($watermarkImage->scale()) {
                $watermarkImageResource = $watermarkImage->getResampleImageResource();

                $watermarkImageWidth = imagesx($watermarkImageResource);
                $watermarkImageHeight = imagesy($watermarkImageResource);

                if (false !== ($watermarkAxis = $watermark->getAxis())) {
                    $watermarkImageAxisX = $watermarkAxis->getX();
                    $watermarkImageAxisY = $watermarkAxis->getY();
                } else {
                    switch ($watermark->getPosition()) {
                        default:
                        case 'MIDDLE_MIDDLE':
                        case 'MIDDLE':
                        case 'CENTER':
                            $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                            $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                            break;

                        case 'MIDDLE_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                            break;

                        case 'MIDDLE_RIGHT':
                            $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                            $watermarkImageAxisY = (imagesy($resampleImageResource) - $watermarkImageHeight) / 2;
                            break;

                        case 'MIDDLE_TOP':
                            $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                            $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                            break;

                        case 'MIDDLE_BOTTOM':
                            $watermarkImageAxisX = (imagesx($resampleImageResource) - $watermarkImageWidth) / 2;
                            $watermarkImageAxisY = imagesy($resampleImageResource) - ($watermarkImageHeight + $watermark->getPadding());
                            break;

                        case 'TOP_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                            break;

                        case 'TOP_RIGHT':
                            $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                            $watermarkImageAxisY = $watermarkImageHeight + $watermark->getPadding();
                            break;

                        case 'BOTTOM_LEFT':
                            $watermarkImageAxisX = $watermark->getPadding();
                            $watermarkImageAxisY = imagesy($resampleImageResource) - $watermarkImageHeight + $watermark->getPadding();
                            break;

                        case 'BOTTOM_RIGHT':
                            $watermarkImageAxisX = imagesx($resampleImageResource) - ($watermarkImageWidth + $watermark->getPadding());
                            $watermarkImageAxisY = imagesy($resampleImageResource) - ($watermarkImageHeight + $watermark->getPadding());
                            break;
                    }
                }

                return imagecopy(
                    $resampleImageResource,
                    $watermarkImageResource,
                    $watermarkImageAxisX,
                    $watermarkImageAxisY,
                    0,
                    0,
                    imagesx($watermarkImageResource),
                    imagesy($watermarkImageResource)
                );
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::createFromSource
     *
     * Create an image resource from source file.
     *
     * @return void
     */
    public function createFromSource()
    {
        /**
         * A work-around for some improperly formatted, but
         * usable JPEGs; known to be produced by Samsung
         * smartphones' front-facing cameras.
         *
         * @see    https://bugs.php.net/bug.php?id=72404
         */
        ini_set('gd.jpeg_ignore_warning', 1);

        $mime = $this->sourceImageFile->getMime();
        $mime = is_array($mime) ? $mime[ 0 ] : $mime;

        try {
            switch ($mime) {
                case 'image/jpg':
                case 'image/jpeg':
                    $this->sourceImageResource = imagecreatefromjpeg($this->sourceImageFile->getRealPath());
                    break;

                case 'image/gif':
                    $this->sourceImageResource = imagecreatefromgif($this->sourceImageFile->getRealPath());
                    break;

                case 'image/png':
                case 'image/x-png':
                    $this->sourceImageResource = imagecreatefrompng($this->sourceImageFile->getRealPath());
                    break;
            }

            // Convert pallete images to true color images
            imagepalettetotruecolor($this->sourceImageResource);
        } catch (\Exception $e) {

        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::scale
     *
     * Scale an image with a given scale.
     *
     * @return bool
     */
    public function scale()
    {
        $sourceDimension = $this->sourceImageFile->getDimension();
        $resampleDimension = $this->resampleImageFile->getDimension();

        $resampleAxis = $this->resampleImageFile->getDimension()->getAxis();
        $sourceAxis = $sourceDimension->getAxis();

        if (function_exists('imagecreatetruecolor')) {
            $this->resampleImageResource = imagecreatetruecolor(
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight()
            );

            imagealphablending($this->resampleImageResource, false);
            imagesavealpha($this->resampleImageResource, true);

            $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
            imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                $resampleDimension->getHeight(), $transparent);

            return imagecopyresampled(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        } else {
            $this->resampleImageResource = imagecreate($resampleDimension->getWidth(),
                $resampleDimension->getHeight());

            imagealphablending($this->resampleImageResource, false);
            imagesavealpha($this->resampleImageResource, true);

            $transparent = imagecolorallocatealpha($this->resampleImageResource, 255, 255, 255, 127);
            imagefilledrectangle($this->resampleImageResource, 0, 0, $resampleDimension->getWidth(),
                $resampleDimension->getHeight(), $transparent);

            return imagecopyresized(
                $this->resampleImageResource,
                $this->sourceImageResource,
                $resampleAxis->getX(),
                $resampleAxis->getY(),
                $sourceAxis->getX(),
                $sourceAxis->getY(),
                $resampleDimension->getWidth(),
                $resampleDimension->getHeight(),
                $sourceDimension->getWidth(),
                $sourceDimension->getHeight()
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * GdDriver::display
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

            $extensions = [
                'image/gif'  => 'gif',
                'image/jpg'  => 'jpg',
                'image/jpeg' => 'jpeg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            $extension = $extensions[ $mime ];
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
     * GdDriver::blob
     *
     * Returns image as string blob.
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
     * GdDriver::save
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

        $extension = pathinfo($imageTargetFilePath, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return (bool)@imagejpeg($resampleImageResource, $imageTargetFilePath, $quality);
                break;

            case 'gif':
                return (bool)@imagegif($resampleImageResource, $imageTargetFilePath);
                break;

            case 'png':
                imagealphablending($resampleImageResource, false);
                imagesavealpha($resampleImageResource, true);

                return (bool)@imagepng($resampleImageResource, $imageTargetFilePath);
                break;

            case 'webp':
                return (bool)@imagewebp($resampleImageResource, $imageTargetFilePath);
                break;
        }

        return false;
    }
}