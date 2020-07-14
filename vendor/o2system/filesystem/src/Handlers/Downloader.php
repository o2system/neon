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

namespace O2System\Filesystem\Handlers;

// ------------------------------------------------------------------------

use O2System\Filesystem\File;

/**
 * Class Downloader
 *
 * @package O2System\Filesystem\Handlers
 */
class Downloader
{
    /**
     * Downloader::MODE_FILESTREAM
     *
     * @var int
     */
    const MODE_FILESTREAM = 1;

    /**
     * Downloader::MODE_DATASTREAM
     *
     * @var int
     */
    const MODE_DATASTREAM = 2;

    /**
     * Downloader::$mode
     *
     * @var int
     */
    protected $mode = 1;

    /**
     * Downloader::$filedata
     *
     * @var string
     */
    protected $filedata;

    /**
     * Downloader::$fileinfo
     *
     * @var array
     */
    protected $fileinfo;

    /**
     * Downloader::$filesize
     *
     * @var int
     */
    protected $filesize;

    /**
     * Downloader::$filemime
     *
     * @var string
     */
    protected $filemime;

    /**
     * Downloader::$lastModified
     *
     * @var int
     */
    protected $lastModified;

    /**
     * Downloader::$resumeable
     *
     * @var bool
     */
    protected $resumeable = true;

    /**
     * Downloader::$partialRequest
     *
     * @var bool
     */
    protected $partialRequest = true;

    /**
     * Downloader::$seekStart
     *
     * @var int
     */
    protected $seekStart = 0;

    /**
     * Downloader::$seekEnd
     *
     * @var int
     */
    protected $seekEnd;

    /**
     * Downloader::$seekFileSize
     *
     * @var int
     */
    protected $seekFileSize;

    /**
     * Downloader::$downloadedFileSize
     *
     * @var int
     */
    protected $downloadedFileSize = 0;

    /**
     * Downloader::$speedLimit
     *
     * @var int
     */
    protected $speedLimit = 512;

    /**
     * Downloader::$bufferSize
     *
     * @var int
     */
    protected $bufferSize = 2048;

    // ------------------------------------------------------------------------

    /**
     * Downloader::__construct
     *
     * @param string $filePath
     * @param int    $mode
     */
    public function __construct($filePath, $mode = self::MODE_FILESTREAM)
    {
        global $HTTP_SERVER_VARS;

        $this->mode = $mode;

        // disables apache compression mod_deflate || mod_gzip
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        // disable php cpmpression
        @ini_set('zlib.output_compression', 'Off');

        if ($mode === self::MODE_FILESTREAM) {
            // Check if File exists and is file or not
            if ( ! is_file($filePath)) {
                output()
                    ->withStatus(404, 'File Not Found')
                    ->send('File Not Found');
            }// Try To Open File for read
            elseif ( ! is_readable($filePath) || ! ($this->filedata = fopen($filePath, 'rb'))) {
                output()
                    ->withStatus(403, 'Forbidden')
                    ->send('File Not Accessible');
            }

            $this->fileinfo = pathinfo($filePath);
            $this->filesize = filesize($filePath);
            $this->filemime = mime_content_type($filePath);
            $this->lastModified = filemtime($filePath);

        } elseif ($mode === self::MODE_DATASTREAM) {
            if (is_file($filePath)) {
                $this->filedata = file_get_contents($filePath);
                $this->fileinfo = pathinfo($filePath);
            } else {
                $this->filedata = $filePath;
                $this->fileinfo = [
                    'dirname'   => null,
                    'basename'  => 'file.stream',
                    'extension' => 'stream',
                    'filename'  => 'file',
                ];
            }

            $this->filesize = strlen($this->filedata);
            $this->filemime = mime_content_type($this->fileinfo[ 'filename' ]);
            $this->lastModified = time();

        } else {
            output()
                ->withStatus(400, 'Bad Request')
                ->send('Undefined Download Mode');
        }

        // Range
        if (isset($_SERVER[ 'HTTP_RANGE' ]) || isset($HTTP_SERVER_VARS[ 'HTTP_RANGE' ])) {
            $this->partialRequest = true;
            $http_range = isset($_SERVER[ 'HTTP_RANGE' ]) ? $_SERVER[ 'HTTP_RANGE' ] : $HTTP_SERVER_VARS[ 'HTTP_RANGE' ];
            if (stripos($http_range, 'bytes') === false) {
                output()
                    ->withStatus(416, 'Requested Range Not Satisfiable')
                    ->send('Requested Range Not Satisfiable');
            }

            $range = substr($http_range, strlen('bytes='));
            $range = explode('-', $range, 3);
            $this->seekStart = ($range[ 0 ] > 0 && $range[ 0 ] < $this->filesize - 1) ? $range[ 0 ] : 0;
            $this->seekEnd = ($range[ 1 ] > 0 && $range[ 1 ] < $this->filesize && $range[ 1 ] > $this->seekStart) ? $range[ 1 ] : $this->filesize - 1;
            $this->seekFileSize = $this->seekEnd - $this->seekStart + 1;
        } else {
            $this->partialRequest = false;
            $this->seekStart = 0;
            $this->seekEnd = $this->filesize - 1;
            $this->seekFileSize = $this->filesize;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Downloader::forceDownload
     *
     * @param string|null $filename
     * @param string      $filemime
     */
    public function forceDownload($filename = null, $filemime = 'application/octet-stream')
    {
        // Force mime
        $this->filemime = $filemime;
        $this->download($filename);
    }

    // ------------------------------------------------------------------------

    /**
     * Downloader::download
     *
     * @param string|null $filename
     */
    public function download($filename = null)
    {
        $filename = isset($filename) ? $filename : $this->fileinfo[ 'basename' ];

        if ($this->partialRequest) {
            if ($this->resumeable) {
                // Turn on resume capability
                output()
                    ->sendHeaderStatus(206, 'Partial Content', '1.0')
                    ->sendHeader('Status', '206 Partial Content')
                    ->sendHeader('Accept-Ranges', 'bytes');

                output()->sendHeader('Content-range',
                    'bytes ' . $this->seekStart . '-' . $this->seekEnd . '/' . $this->filesize);
            } else {
                // Turn off resume capability
                $this->seekStart = 0;
                $this->seekEnd = $this->filesize - 1;
                $this->seekFileSize = $this->filesize;
            }
        }

        // Common Download Headers content type, content disposition, content length and last modified
        output()
            ->sendHeader('Content-Type', $this->filemime)
            ->sendHeader('Content-Disposition', 'attachment; filename=' . $filename)
            ->sendHeader('Content-Length', $this->seekFileSize)
            ->sendHeader('Last-Modified', date('D, d M Y H:i:s \G\M\T', $this->lastModified));
        // End Headers Stage

        // Work On Download Speed Limit
        if ($this->speedLimit) {
            // how many buffers ticks per second
            $bufferTicks = 10;    //10
            // how long one buffering tick takes by micro second
            $bufferMicroTime = 150; // 100
            // Calculate sleep micro time after each tick
            $sleepMicroTime = round((1000000 - ($bufferTicks * $bufferMicroTime)) / $bufferTicks);
            // Calculate required buffer per one tick, make sure it is integer so round the result
            $this->bufferSize = round($this->speedLimit * 1024 / $bufferTicks);
        }
        // Immediatly Before Downloading
        // clean any output buffer
        @ob_end_clean();

        // get oignore_user_abort value, then change it to yes
        $oldUserAbortSetting = ignore_user_abort();
        ignore_user_abort(true);
        // set script execution time to be unlimited
        @set_time_limit(0);


        // Download According Download Mode
        if ($this->mode === self::MODE_FILESTREAM) {
            // Download Data by fopen
            $downloadFileBytes = $this->seekFileSize;
            $downloaded = 0;
            // goto the position of the first byte to download
            fseek($this->filedata, $this->seekStart);
            while ($downloadFileBytes > 0 && ! (connection_aborted() || connection_status() == 1)) {
                // still Downloading
                if ($downloadFileBytes > $this->bufferSize) {
                    // send buffer size
                    echo fread($this->filedata, $this->bufferSize); // this also will seek to after last read byte
                    $downloaded += $this->bufferSize;    // updated downloaded
                    $downloadFileBytes -= $this->bufferSize;    // update remaining bytes
                } else {
                    // send required size
                    // this will happens when we reaches the end of the file normally we wll download remaining bytes
                    echo fread($this->filedata, $downloadFileBytes);    // this also will seek to last reat

                    $downloaded += $downloadFileBytes;    // Add to downloaded


                    $downloadFileBytes = 0;    // Here last bytes have been written
                }
                // send to buffer
                flush();
                // Check For Download Limit
                if ($this->speedLimit) {
                    usleep($sleepMicroTime);
                }


            }
            // all bytes have been sent to user
            // Close File
            fclose($this->filedata);
        } elseif ($this->mode === self::MODE_DATASTREAM) {
            // Download Data String
            $downloadFileBytes = $this->seekFileSize;

            $downloaded = 0;
            $offset = $this->seekStart;
            while ($downloadFileBytes > 0 && ( ! connection_aborted())) {
                if ($downloadFileBytes > $this->bufferSize) {
                    // Download by buffer
                    echo mb_strcut($this->filedata, $offset, $this->bufferSize);
                    $downloadFileBytes -= $this->bufferSize;
                    $downloaded += $this->bufferSize;
                    $offset += $this->bufferSize;
                } else {
                    // download last bytes
                    echo mb_strcut($this->filedata, $offset, $downloadFileBytes);
                    $downloaded += $downloadFileBytes;
                    $offset += $downloadFileBytes;
                    $downloadFileBytes = 0;
                }
                // Send Data to Buffer
                flush();
                // Check Limit
                if ($this->speedLimit) {
                    usleep($sleepMicroTime);
                }

            }
        }

        // Set Downloaded Bytes
        $this->downloadedFileSize = $downloaded;
        ignore_user_abort($oldUserAbortSetting); // Restore old user abort settings
        set_time_limit(ini_get('max_execution_time')); // Restore Default script max execution Time

        exit;
    }

    // ------------------------------------------------------------------------

    /**
     * Downloader::resumeable
     *
     * @param bool $status
     *
     * @return static
     */
    public function resumeable($status = true)
    {
        $this->partialRequest = $this->resumeable = ( bool )$status;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Downloader::speedLimit
     *
     * @param int $limit
     *
     * @return static
     */
    public function speedLimit($limit)
    {
        $limit = intval($limit);
        $this->speedLimit = $limit;

        return $this;
    }
}