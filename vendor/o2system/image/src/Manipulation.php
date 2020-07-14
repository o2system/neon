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

use O2System\Image\Abstracts\AbstractDriver;
use O2System\Image\Abstracts\AbstractWatermark;
use O2System\Image\DataStructures\Config;
use O2System\Image\Optimizers\Imageoptim;
use O2System\Spl\Exceptions\Runtime\FileNotFoundException;

/**
 * Class Manipulation
 *
 * @package O2System\Image
 */
class Manipulation
{
    /**
     * Manipulation::ROTATE_CW
     *
     * Clock wise image rotation degrees.
     *
     * @var int
     */
    const ROTATE_CW = 90;

    /**
     * Manipulation::ROTATE_CCW
     *
     * Counter clock wise image rotation degrees.
     *
     * @var int
     */
    const ROTATE_CCW = -90;

    /**
     * Manipulation::FLIP_HORIZONTAL
     *
     * Flip image with horizontal axis.
     *
     * @var int
     */
    const FLIP_HORIZONTAL = 1;

    /**
     * Manipulation::FLIP_VERTICAL
     *
     * Flip image with vertical axis.
     *
     * @var int
     */
    const FLIP_VERTICAL = 2;

    /**
     * Manipulation::FLIP_BOTH
     *
     * Flip image with horizontal and vertical axis.
     *
     * @var int
     */
    const FLIP_BOTH = 3;

    /**
     * Manipulation::ORIENTATION_AUTO
     *
     * Auto image orientation.
     *
     * @var string
     */
    const ORIENTATION_AUTO = 'AUTO';

    /**
     * Manipulation::ORIENTATION_LANDSCAPE
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_LANDSCAPE = 'LANDSCAPE';

    /**
     * Manipulation::ORIENTATION_PORTRAIT
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_PORTRAIT = 'PORTRAIT';
    
    /**
     * Manipulation::ORIENTATION_SQUARE
     *
     * Landscape image orientation.
     *
     * @var string
     */
    const ORIENTATION_SQUARE = 'SQUARE';

    /**
     * Manipulation::DIRECTIVE_UP
     *
     * If the target image size is larger than the size of the source image then the image source size
     * will be resized according to the target image size. But if the target image size is smaller than
     * the size of the source image then the resulting image size will match the size of the image source.
     * In other words the target image size should not be smaller than the size of the source image.
     *
     * @var string
     */
    const DIRECTIVE_UP = 'UP';

    /**
     * Manipulation::DIRECTIVE_DOWN
     *
     * If the target image size is smaller than the size of the source image then the image source size
     * will be resized according to the target image size. But if the target image size is larger than
     * the size of the source image then the resulting image size will match the size of the image source.
     * In other words the target image size can not be larger than the size of the image source.
     */
    const DIRECTIVE_DOWN = 'DOWN';

    /**
     * Manipulation::DIRECTIVE_RATIO
     *
     * The source image size will always resized according to the target image size and according to aspect ratio.
     *
     * @var string
     */
    const DIRECTIVE_RATIO = 'RATIO';

    /**
     * Manipulation::WATERMARK_CENTER
     *
     * Watermark center position.
     *
     * @var string
     */
    const WATERMARK_CENTER = 'CENTER';

    /**
     * Manipulation::WATERMARK_MIDDLE_LEFT
     *
     * Watermark middle left position.
     *
     * @var string
     */
    const WATERMARK_MIDDLE_LEFT = 'MIDDLE_LEFT';

    /**
     * Manipulation::WATERMARK_MIDDLE_RIGHT
     *
     * Watermark middle right position.
     *
     * @var string
     */
    const WATERMARK_MIDDLE_RIGHT = 'MIDDLE_RIGHT';

    /**
     * Manipulation::WATERMARK_MIDDLE_TOP
     *
     * Watermark middle top position.
     *
     * @var string
     */
    const WATERMARK_MIDDLE_TOP = 'MIDDLE_TOP';

    /**
     * Manipulation::WATERMARK_MIDDLE_BOTTOM
     *
     * Watermark middle bottom position.
     *
     * @var string
     */
    const WATERMARK_MIDDLE_BOTTOM = 'MIDDLE_BOTTOM';

    /**
     * Manipulation::WATERMARK_TOP_LEFT
     *
     * Watermark top left position.
     *
     * @var string
     */
    const WATERMARK_TOP_LEFT = 'TOP_LEFT';

    /**
     * Manipulation::WATERMARK_TOP_RIGHT
     *
     * Watermark top right position.
     *
     * @var string
     */
    const WATERMARK_TOP_RIGHT = 'TOP_RIGHT';

    /**
     * Manipulation::WATERMARK_BOTTOM_LEFT
     *
     * Watermark bottom left position.
     *
     * @var string
     */
    const WATERMARK_BOTTOM_LEFT = 'BOTTOM_LEFT';

    /**
     * Manipulation::WATERMARK_BOTTOM_RIGHT
     *
     * Watermark bottom right position.
     *
     * @var string
     */
    const WATERMARK_BOTTOM_RIGHT = 'BOTTOM_RIGHT';

    /**
     * Manipulation::$config
     *
     * Manipulation image config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Manipulation::$driver
     *
     * Manipulation image driver.
     *
     * @var AbstractDriver
     */
    protected $driver;

    // ------------------------------------------------------------------------

    /**
     * Manipulation::__construct
     *
     * @param \O2System\Image\DataStructures\Config $config
     */
    public function __construct(Config $config = null)
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('image');

        $this->config = is_null($config) ? new Config() : $config;

        if ($this->config->offsetExists('driver')) {
            $this->loadDriver($this->config->driver);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::loadDriver
     *
     * Internal driver loader.
     *
     * @param string $driverOffset Driver offset name.
     *
     * @return bool
     */
    protected function loadDriver($driverOffset)
    {
        $driverClassName = '\O2System\Image\Drivers\\' . ucfirst($driverOffset) . 'Driver';

        if (class_exists($driverClassName)) {
            if ($this->config->offsetExists($driverOffset)) {
                $config = $this->config[ $driverOffset ];
            } else {
                $config = $this->config->getArrayCopy();
            }

            if (isset($config[ 'engine' ])) {
                unset($config[ 'engine' ]);
            }

            $this->driver = new $driverClassName();

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setDriver
     *
     * Manually set image manipulation library driver.
     *
     * @param \O2System\Image\Abstracts\AbstractDriver $imageDriver
     *
     * @return static
     */
    public function setDriver(AbstractDriver $imageDriver)
    {
        $this->driver = $imageDriver;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setImageFile
     *
     * Sets image for manipulation.
     *
     * @param string $imageFilePath Existing image file path.
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    public function setImageFile($imageFilePath)
    {
        if ( ! $this->driver->setSourceImage($imageFilePath)) {
            throw new FileNotFoundException('IMAGE_E_FILE_NOT_FOUND', 0, [$imageFilePath]);
        }

        // Create image source resource
        $this->driver->createFromSource();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setImageUrl
     *
     * @param string $imageUrl
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    public function setImageUrl($imageUrl)
    {
        if (false === ($imageString = file_get_contents($imageUrl))) {
            throw new FileNotFoundException('IMAGE_E_URL_INVALID', 0, [$imageUrl]);
        }

        // Create image source resource
        $this->driver->createFromString($imageString);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::setImageString
     *
     * @param string $imageString Image string.
     * @param bool   $base64      Use base64_decode to decode the image string.
     *
     * @return static
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    public function setImageString($imageString, $base64 = false)
    {
        if ($base64) {
            if (false === ($imageString = base64_decode($imageString, true))) {
                throw new FileNotFoundException('IMAGE_E_STRING_INVALID');
            }
        }

        // Create image source resource
        $this->driver->createFromString($imageString);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::rotateImage
     *
     * Rotate an image.
     *
     * @param int $degrees Image rotation degrees.
     *
     * @return bool
     */
    public function rotateImage($degrees)
    {
        if (is_int($degrees)) {
            $this->driver->rotate($degrees);

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::flipImage
     *
     * Flip an image.
     *
     * @param int $axis Image flip axis.
     *
     * @return bool
     */
    public function flipImage($axis)
    {
        if (in_array($axis, [self::FLIP_HORIZONTAL, self::FLIP_VERTICAL, self::FLIP_BOTH])) {

            $this->driver->flip($axis);

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::resizeImage
     *
     * Scale an image using the given new width and height
     *
     * @param int  $newWidth  The width to scale the image to.
     * @param int  $newHeight The height to scale the image to.
     * @param bool $crop      The image autocrop
     *
     * @return bool
     */
    public function resizeImage($newWidth, $newHeight, $crop = false)
    {
        $newWidth = intval($newWidth);
        $newHeight = intval($newHeight);
        $resampleImageFile = $this->driver->getSourceImageFile();
        $resampleDimension = $resampleImageFile->getDimension();
        $resampleDimension->maintainAspectRatio = $this->config->offsetGet('maintainAspectRatio');

        if ($newWidth == $newHeight) {
            $this->driver->setResampleImage($resampleImageFile->withDimension(
                $resampleDimension
                    ->withOrientation('SQUARE')
                    ->withFocus('CENTER')
                    ->withSize($newWidth, $newHeight)
            ));

            return $this->driver->resize(true);
        } else {
            $this->driver->setResampleImage($resampleImageFile->withDimension(
                $resampleDimension
                    ->withDirective($this->config->offsetGet('scaleDirective'))
                    ->withOrientation($this->config->offsetGet('orientation'))
                    ->withFocus($this->config->offsetGet('focus'))
                    ->withSize($newWidth, $newHeight)
            ));

            return $this->driver->resize($crop);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::scaleImage
     *
     * Scale an image using the percentage image scale.
     *
     * @param int $newScale The percentage to scale the image to.
     *
     * @return bool
     */
    public function scaleImage($newScale)
    {
        $resampleImageFile = $this->driver->getSourceImageFile();
        $resampleDimension = $resampleImageFile->getDimension();
        $resampleDimension->maintainAspectRatio = $this->config->offsetGet('maintainAspectRatio');

        $this->driver->setResampleImage($resampleImageFile->withDimension(
            $resampleDimension
                ->withScale($newScale)
        ));

        return $this->driver->scale();
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::watermarkImage
     *
     * Watermark an image using Text or Overlay.
     *
     * @param \O2System\Image\Abstracts\AbstractWatermark $watermark
     */
    public function watermarkImage(AbstractWatermark $watermark)
    {
        $this->driver->watermark($watermark);
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::cropImage
     *
     * Crop an image using new Dimension.
     *
     * @param \O2System\Image\Dimension $dimension
     */
    public function cropImage(Dimension $dimension)
    {
        $this->driver->crop($dimension);
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::getBlobImage
     *
     * Gets image blob string.
     *
     * @return string
     */
    public function getBlobImage()
    {
        return $this->driver->blob($this->config->offsetGet('quality'));
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::displayImage
     *
     * Display an image.
     *
     * @return void
     */
    public function displayImage($quality = null, $mime = null)
    {
        $quality = empty($quality) ? $this->config->offsetGet('quality') : $quality;

        $filename = pathinfo($this->driver->getSourceImageFile()->getBasename(), PATHINFO_FILENAME);
        $extension = pathinfo($this->driver->getSourceImageFile()->getBasename(), PATHINFO_EXTENSION);

        if (empty($mime)) {
            $mime = $this->driver->getSourceImageFile()->getMime();
            $mime = is_array($mime) ? $mime[ 0 ] : $mime;

            $extension = $this->driver->getMimeExtension($mime);
        }

        if ($this->saveImage($tempImageFilePath = rtrim(sys_get_temp_dir(),
                DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename . '.' . $extension,
            $quality)
        ) {
            $imageBlob = readfile($tempImageFilePath);
            unlink($tempImageFilePath);
        }

        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Content-Disposition: filename=' . $filename . '.' . $extension);
        header('Content-Type: ' . $mime);

        echo $imageBlob;
        exit(0);
    }

    // ------------------------------------------------------------------------

    /**
     * Manipulation::saveImage
     *
     * Save an manipulated image into new image file.
     *
     * @param string $saveImageFilePath Save image file path.
     *
     * @return bool
     */
    public function saveImage($saveImageFilePath, $quality = null)
    {
        $quality = empty($quality) ? $this->config->offsetGet('quality') : $quality;
        $optimizerConfig = $this->config->offsetGet('optimizer');

        if ($this->driver->save($saveImageFilePath, $quality)) {
            if ($optimizerConfig === 'default') {
                $optimizer = new Optimizer();
                $optimizer->optimize($saveImageFilePath);
            } elseif ( ! empty($optimizerConfig[ 'factory' ])) {
                $factory = $optimizerConfig[ 'factory' ];
                $optimizer = new Optimizer();

                switch ($factory) {
                    case 'imageoptim';
                        $factory = new Imageoptim();
                        $factory->setUsername($optimizerConfig[ 'username' ]);
                        $optimizer->setImageFactory($factory);
                        break;
                }

                $optimizer->optimize($saveImageFilePath);
            }

            return true;
        }

        return false;
    }
}