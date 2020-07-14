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

namespace O2System\Filesystem;

// ------------------------------------------------------------------------

use O2System\Filesystem\Handlers\Stream;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class File
 *
 * @package O2System\Filesystem
 */
class File extends SplFileInfo
{
    /**
     * File::$filePath
     *
     * Path of File
     * @var String
     */
    private $filePath;

    // ------------------------------------------------------------------------

    /**
     * File::__construct
     *
     * @param string|null $filePath
     */
    public function __construct($filePath = null)
    {
        if (isset($filePath)) {
            $this->filePath = $filePath;
            parent::__construct($filePath);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * File::setGroup
     *
     * Attempts to change the group of the file filename to group.
     *
     * Only the superuser may change the group of a file arbitrarily; other users may change the group of a file to any
     * group of which that user is a member.
     *
     * @param mixed $group A group name or number.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setGroup($group)
    {
        $params[] = $this->getRealPath();
        $params[] = $group;

        return call_user_func_array('chgrp', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::setMode
     *
     * Attempts to change the mode of the specified file to that given in mode.
     *
     * @param int $mode The mode parameter consists of three octal number components specifying access restrictions for
     *                  the owner, the user group in which the owner is in, and to everybody else in this order. One
     *                  component can be computed by adding up the needed permissions for that target user base. Number
     *                  1 means that you grant execute rights, number 2 means that you make the file writable, number
     *                  4 means that you make the file readable. Add up these numbers to specify needed rights. You can
     *                  also read more about modes on Unix systems with 'man 1 chmod' and 'man 2 chmod'.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setMode($mode)
    {
        $params[] = $this->getRealPath();
        $params[] = $mode;

        return call_user_func_array('chmod', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::setOwner
     *
     * Attempts to change the owner of the file filename to user user.
     * Only the superuser may change the owner of a file.
     *
     * @param mixed $user A user name or number.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setOwner($user)
    {
        $params[] = $this->getRealPath();
        $params[] = $user;

        return call_user_func_array('chown', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::setLink
     *
     * Creates a symbolic link to the file with the specified name link.
     *
     * @param string $link The link name.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setLink($link)
    {
        $params[] = $this->getRealPath();
        $params[] = $link;

        return call_user_func_array('symlink', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::getContents
     *
     * Reads entire file into a string.
     *
     * @param bool     $useIncludePath As of PHP 5 the FILE_USE_INCLUDE_PATH constant can be used to trigger include
     *                                 path search.
     *
     * @param resource $context        A valid context resource created with stream_context_create(). If you don't need
     *                                 to use a custom context, you can skip this parameter by NULL.
     *
     * @param int      $offset         The offset where the reading starts on the original stream. Negative offsets
     *                                 count from the end of the stream. Seeking (offset) is not supported with remote
     *                                 files. Attempting to seek on non-local files may work with small offsets, but
     *                                 this is unpredictable because it works on the buffered stream.
     * @param int      $maxlen         Maximum length of data read. The default is to read until end of file is
     *                                 reached. Note that this parameter is applied to the stream processed by the
     *                                 filters.
     *
     * @return string   The function returns the read data or FALSE on failure.
     */
    public function getContents($useIncludePath = false, $context = null, $offset = 0, $maxlen = 0)
    {
        $params[] = $this->getRealPath();
        $params[] = $useIncludePath;
        $params[] = $context;
        $params[] = $offset;

        if ($maxlen > 0) {
            $params[] = $maxlen;
        }

        return call_user_func_array('file_get_contents', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::touch
     *
     * @param int $time  The touch time. If time is not supplied, the current system time is used.
     * @param int $atime If present, the access time of the given filename is set to the value of atime. Otherwise, it
     *                   is set to the value passed to the time parameter. If neither are present, the current system
     *                   time is used.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function touch($time = null, $atime = null)
    {
        $params[] = $this->getRealPath();
        $params[] = (isset($time) ? $time : time());
        $params[] = (isset($atime) ? $atime : time());

        return call_user_func_array('touch', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::show
     *
     * Show file with header.
     *
     * @return void
     */
    public function show()
    {
        if ($mime = $this->getMime()) {
            $mime = is_array($mime) ? $mime[ 0 ] : $mime;
        } elseif (is_file($this->getRealPath())) {
            $mime = 'application/octet-stream';
        }

        $fileSize = filesize($this->getRealPath());
        $filename = pathinfo($this->getRealPath(), PATHINFO_BASENAME);

        // Common headers
        $expires = 604800; // (60*60*24*7)
        header('Expires:' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        header('Accept-Ranges: bytes', true);
        header("Cache-control: private", true);
        header('Pragma: private', true);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        $ETag = '"' . md5($filename) . '"';

        if ( ! empty($_SERVER[ 'HTTP_IF_NONE_MATCH' ])
            && $_SERVER[ 'HTTP_IF_NONE_MATCH' ] == $ETag
        ) {
            header('HTTP/1.1 304 Not Modified');
            header('Content-Length: ' . $fileSize);
            exit;
        }

        $expires = 604800; // (60*60*24*7)
        header('ETag: ' . $ETag);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Expires:' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        // Open file
        // @readfile($file_path);
        $file = @fopen($filename, "rb");
        if ($file) {
            while ( ! feof($file)) {
                print(fread($file, 1024 * 8));
                flush();
                if (connection_status() != 0) {
                    @fclose($file);
                    die();
                }
            }
            @fclose($file);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * File::getMime
     *
     * Get mime info based on MIME config.
     *
     * @return bool|string|array
     */
    public function getMime()
    {
        $mimes = $this->getMimes();
        $ext = strtolower($this->getExtension());

        if (isset($mimes[ $ext ])) {
            return $mimes[ $ext ];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * File::getMimes
     *
     * Get config mimes.
     *
     * @static
     * @return array Returns the MIME types array from config/mimes.php
     */
    public static function getMimes()
    {
        static $mimes;

        if (empty($mimes)) {
            if (file_exists(__DIR__ . '/Config/Mimes.php')) {
                $mimes = require(__DIR__ . '/Config/Mimes.php');
            }
        }

        return $mimes;
    }

    // ------------------------------------------------------------------------

    /**
     * File::read
     *
     * Outputs a file.
     *
     * @param bool     $useIncludePath You can use the optional second parameter and set it to TRUE, if you want to
     *                                 search for the file in the include_path, too.
     * @param resource $context        A context stream resource.
     *
     * @return int  Returns the number of bytes read from the file. If an error occurs, FALSE is returned and unless
     *              the function was called as @readfile(), an error message is printed.
     */
    public function read($useIncludePath = false, $context = null)
    {
        $params[] = $this->getRealPath();
        $params[] = $useIncludePath;
        $params[] = $context;

        return call_user_func_array('readfile', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::write
     *
     * Writes a file.
     *
     * @param string $contents File contents to write.
     * @param string $mode     File handle mode.
     *
     * @return bool
     */
    public function write($filePath, $contents, $mode = 'wb')
    {
        if (false !== ($fp = $this->create($filePath, $mode))) {
            flock($fp, LOCK_EX);

            for ($result = $written = 0, $length = strlen($contents); $written < $length; $written += $result) {
                if (($result = fwrite($fp, substr($contents, $written))) === false) {
                    break;
                }
            }

            flock($fp, LOCK_UN);
            fclose($fp);

            return is_int($result);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * File::create
     *
     * Create a File
     *
     * @param  string|null $filePath
     * @param  string      $mode
     *
     * @return resource
     */
    public function create($filePath = null, $mode = 'wb')
    {
        $filePath = isset($filePath) ? $filePath : $this->filePath;
        $dir = dirname($filePath);

        if ( ! is_writable($dir)) {
            if ( ! file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        if ( ! $fp = @fopen($filePath, $mode)) {
            return false;
        }

        parent::__construct($filePath);

        return $fp;
    }

    // ------------------------------------------------------------------------

    /**
     * File::rename
     *
     * Renames a file.
     *
     * @param string $newFilename The new filename.
     * @param null   $context     Context support was added with PHP 5.0.0. For a description of contexts, refer to
     *                            Streams.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rename($newFilename, $context = null)
    {
        $params[] = $this->getRealPath();
        $params[] = dirname($this->getRealPath()) . DIRECTORY_SEPARATOR . $newFilename;
        $params[] = $context;

        return call_user_func_array('rename', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::copy
     *
     * Makes a copy of the file source to destination.
     *
     * @param string   $destination The destination path. If destination is a URL, the copy operation may fail if the
     *                              wrapper does not support overwriting of existing files.
     * @param resource $context     A valid context resource created with stream_context_create().
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function copy($destination, $context = null)
    {
        $params[] = $this->getRealPath();
        $params[] = $destination;
        $params[] = $context;

        return call_user_func_array('copy', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::delete
     *
     * Deletes a file.
     *
     * @param resource $context Context support was added with PHP 5.0.0. For a description of contexts, refer to
     *                          Streams.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function delete($context = null)
    {
        $params[] = $this->getRealPath();
        $params[] = $context;

        return call_user_func_array('unlink', $params);
    }

    // ------------------------------------------------------------------------

    /**
     * File::getStream
     *
     * @param string $mode
     * @return Stream
     */
    public function getStream($mode = 'rb')
    {
        return new Stream($this, $mode);
    }
}