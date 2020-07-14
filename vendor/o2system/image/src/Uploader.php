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

use O2System\Kernel\Http\Message\UploadFile;

/**
 * Class Upload
 *
 * @package O2System\Image
 */
class Uploader extends \O2System\Filesystem\Handlers\Uploader
{
    /**
     * Uploader::$allowedMimes
     *
     * Allowed uploaded file mime types.
     *
     * @var array
     */
    protected $allowedMimes = [
        IMAGETYPE_GIF  => 'image/gif',
        IMAGETYPE_JPEG => 'image/jpeg',
        IMAGETYPE_PNG  => 'image/png',
    ];

    /**
     * Uploader::$allowedExtensions
     *
     * Allowed uploaded file extensions.
     *
     * @var array
     */
    protected $allowedExtensions = ['.gif', '.jpg', '.jpeg', '.png'];

    /**
     * Uploader::$allowedImageSize
     *
     * Allowed uploaded image size.
     *
     * @var array
     */
    protected $allowedImageSize = [
        'width'  => [
            'min' => 0,
            'max' => 0,
        ],
        'height' => [
            'min' => 0,
            'max' => 0,
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Uploader::__construct
     *
     * @param array $config
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function __construct(array $config = [])
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('image');

        parent::__construct($config);
    }

    // ------------------------------------------------------------------------

    /**
     * Uploader::setMinWidthSize
     *
     * @param int $size
     *
     * @return static
     */
    public function setMinWidthSize($size)
    {
        $this->allowedImageSize[ 'width' ][ 'min' ] = (int)$size;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Uploader::setMaxWidthSize
     *
     * @param int $size
     *
     * @return static
     */
    public function setMaxWidthSize($size)
    {
        $this->allowedImageSize[ 'width' ][ 'max' ] = (int)$size;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Uploader::setMinHeightSize
     *
     * @param int $size
     *
     * @return static
     */
    public function setMinHeightSize($size)
    {
        $this->allowedImageSize[ 'height' ][ 'min' ] = (int)$size;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Uploader::setMaxHeightSize
     *
     * @param int $size
     *
     * @return static
     */
    public function setMaxHeightSize($size)
    {
        $this->allowedImageSize[ 'height' ][ 'max' ] = (int)$size;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Uploader::validate
     *
     * @param \O2System\Kernel\Http\Message\UploadFile $file
     *
     * @return bool
     */
    protected function validate(UploadFile $file)
    {
        if (parent::validate($file)) {

            $info = getimagesize($file->getFileTemp());
            $width = $info[ 0 ];
            $height = $info[ 1 ];

            /* Validate width min size */
            if ($this->allowedImageSize[ 'width' ][ 'min' ] > 0) {
                if ($width < $this->allowedImageSize[ 'width' ][ 'min' ]) {
                    $this->errors[] = language()->getLine(
                        'IMAGE_E_ALLOWED_MIN_WIDTH_SIZE',
                        [$this->allowedFileSize[ 'min' ], $width]
                    );
                }
            }

            /* Validate width max size */
            if ($this->allowedImageSize[ 'width' ][ 'max' ] > 0) {
                if ($width > $this->allowedImageSize[ 'width' ][ 'max' ]) {
                    $this->errors[] = language()->getLine(
                        'IMAGE_E_ALLOWED_MAX_WIDTH_SIZE',
                        [$this->allowedFileSize[ 'max' ], $width]
                    );
                }
            }

            /* Validate height min size */
            if ($this->allowedImageSize[ 'height' ][ 'min' ] > 0) {
                if ($height < $this->allowedImageSize[ 'width' ][ 'min' ]) {
                    $this->errors[] = language()->getLine(
                        'IMAGE_E_ALLOWED_MIN_HEIGHT_SIZE',
                        [$this->allowedFileSize[ 'min' ], $height]
                    );
                }
            }

            /* Validate height max size */
            if ($this->allowedImageSize[ 'height' ][ 'max' ] > 0) {
                if ($height > $this->allowedImageSize[ 'width' ][ 'max' ]) {
                    $this->errors[] = language()->getLine(
                        'IMAGE_E_ALLOWED_MAX_HEIGHT_SIZE',
                        [$this->allowedFileSize[ 'max' ], $height]
                    );
                }
            }
        }

        if (count($this->errors) == 0) {
            return true;
        }

        return false;
    }
}