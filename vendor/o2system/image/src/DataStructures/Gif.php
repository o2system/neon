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

namespace O2System\Image\DataStructures;

// ------------------------------------------------------------------------

use O2System\Filesystem\Handlers\Stream;
use O2System\Image\File;

/**
 * Class Gif
 * @package O2System\Image\DataStructures
 */
class Gif
{
    /**
     * Gif::$decode
     *
     * @var bool
     */
    protected $decode = false;

    /**
     * Gif::$stream
     *
     * @var Stream
     */
    protected $stream;

    /**
     * Gif::$metadata
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * Gif::$originalMetadata
     *
     * @var array
     */
    protected $originalMetadata = [];

    // ------------------------------------------------------------------------

    /**
     * Gif::__construct
     */
    public function __construct(File $file)
    {
        if (false !== ($this->stream = $file->getStream())) {
            $this->parse();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Gif::parse
     */
    protected function parse()
    {
        $this->decode = true;
        $this->parseHeader();
    }

    //-------------------------------------------------------

    /**
     * Gif::parseHeader
     */
    protected function parseHeader()
    {
        $this->stream->forward(10);
        if ($this->readBits(($byteInteger = $this->stream->readByteInteger()), 0, 1) == 1) {
            $this->stream->forward(2);
            $this->stream->forward(pow(2, $this->readBits($byteInteger, 5, 3) + 1) * 3);
        } else {
            $this->stream->forward(2);
        }

        $this->metadata['header'] = $this->readPartOfData(0, $this->stream->getPointer());

        if ($this->decode) {
            $this->originalMetadata['header'] = $this->metadata['header'];
            $this->originalMetadata['width'] = ord($this->originalMetadata['header'][7]) * 256 + ord($this->originalMetadata['header'][6]);
            $this->originalMetadata['height'] = ord($this->originalMetadata['header'][9]) * 256 + ord($this->originalMetadata['header'][8]);
            $this->originalMetadata["background_color"] = $this->originalMetadata['header'][11];
        }
    }

    //-------------------------------------------------------

    /**
     * Gif::parseExtension
     */
    protected function parseExtension()
    {
        $startdata = $this->stream->readByteOfData(2);
        if ($startdata == chr(0x21) . chr(0xf9)) {
            $start = $this->stream->getPointer() - 2;
            $this->stream->forward($this->stream->readByteInteger());
            $this->stream->forward(1);
            if ($type == 2) {
                $this->imageData[$this->index]["graphicsextension"] = $this->readPartOfData($start,
                    $this->stream->getPointer() - $start);
            } else {
                if ($type == 1) {
                    $this->originalVariables["hasgx_type_1"] = 1;
                    $this->globalData["graphicsextension"] = $this->readPartOfData($start,
                        $this->stream->getPointer() - $start);
                } else {
                    if ($type == 0 && $this->decoding == false) {
                        $this->encdata[$this->index]["graphicsextension"] = $this->readPartOfData($start,
                            $this->stream->getPointer() - $start);
                    } else {
                        if ($type == 0 && $this->decoding == true) {
                            $this->originalVariables["hasgx_type_0"] = 1;
                            $this->globalData["graphicsextension_0"] = $this->readPartOfData($start,
                                $this->stream->getPointer() - $start);
                        }
                    }
                }
            }
        } else {
            $this->stream->rewind(2);
        }
    }

    //-------------------------------------------------------

    /**
     * Gif::parseBlock
     * 
     * @param $type
     */
    protected function parseBlock($type)
    {
        if ($this->checkByte(0x2c)) {
            $start = $this->stream->getPointer();
            $this->stream->forward(9);
            if ($this->readbits(($mybyte = $this->stream->readByteInteger()), 0, 1) == 1) {
                $this->stream->forward(pow(2, $this->readBits($mybyte, 5, 3) + 1) * 3);
            }
            $this->stream->forward(1);
            $this->readDataStream($this->stream->readByteInteger());
            $this->imageData[$this->index]["imagedata"] = $this->readPartOfData($start, $this->stream->getPointer() - $start);

            if ($type == 0) {
                $this->originalVariables["hasgx_type_0"] = 0;
                if (isset($this->globalData["graphicsextension_0"])) {
                    $this->imageData[$this->index]["graphicsextension"] = $this->globalData["graphicsextension_0"];
                } else {
                    $this->imageData[$this->index]["graphicsextension"] = null;
                }
                unset($this->globalData["graphicsextension_0"]);
            } elseif ($type == 1) {
                if (isset($this->originalVariables["hasgx_type_1"]) && $this->originalVariables["hasgx_type_1"] == 1) {
                    $this->originalVariables["hasgx_type_1"] = 0;
                    $this->imageData[$this->index]["graphicsextension"] = $this->globalData["graphicsextension"];
                    unset($this->globalData["graphicsextension"]);
                } else {
                    $this->originalVariables["hasgx_type_0"] = 0;
                    $this->imageData[$this->index]["graphicsextension"] = $this->globalData["graphicsextension_0"];
                    unset($this->globalData["graphicsextension_0"]);
                }
            }

            $this->parse_image_data();
            $this->index++;

        }
    }

    //-------------------------------------------------------

    /**
     * Gif::parseApplicationData
     */
    protected function parseApplicationData()
    {
        $startdata = $this->readbyte(2);
        if ($startdata == chr(0x21) . chr(0xff)) {
            $start = $this->stream->getPointer() - 2;
            $this->stream->forward($this->stream->readByteInteger());
            $this->readDataStream($this->stream->readByteInteger());
            $this->imageinfo["applicationdata"] = $this->readPartOfData($start, $this->stream->getPointer() - $start);
        } else {
            $this->stream->rewind(2);
        }
    }

    //-------------------------------------------------------

    /**
     * Gif::parseCommentData
     */
    protected function parseCommentData()
    {
        $startData = $this->stream->readByteOfData(2);
        if ($startData == chr(0x21) . chr(0xfe)) {
            $start = $this->stream->getPointer() - 2;
            $this->readDataStream($this->stream->readByteInteger());
            $this->imageinfo["commentdata"] = $this->readPartOfData($start, $this->stream->getPointer() - $start);
        } else {
            $this->stream->rewind(2);
        }
    }
    //-------------------------------------------------------

    /**
     * Gif::readBits
     *
     * @param string $byte
     * @param int $start
     * @param int $length
     * @return float|int
     */
    private function readBits($byte, $start, $length)
    {
        $bin = str_pad(decbin($byte), 8, "0", STR_PAD_LEFT);
        $data = substr($bin, $start, $length);
        return bindec($data);
    }

    //-------------------------------------------------------

    /**
     * Gif::readPartOfData
     *
     * @param int $start
     * @param in $length
     * @return false|string
     */
    public function readPartOfData($start, $length)
    {
        $stream = clone $this->stream;
        $resource = $stream->getResource();

        fseek($resource, $start);
        $data = fread($resource, $length);
        return $data;
    }

    //-------------------------------------------------------

    /**
     * Gif::readDataStream
     *
     * @param $firstLength
     * @return bool
     */
    protected function readDataStream($firstLength)
    {
        $this->stream->forward($firstLength);
        $length = $this->stream->readByteInteger();
        if ($length != 0) {
            while ($length != 0) {
                $this->stream->forward($length);
                $length = $this->stream->readByteInteger();
            }
        }
        return true;
    }
}