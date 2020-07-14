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

use O2System\Spl\Exceptions\RuntimeException;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Ftp
 *
 * @package O2System\Filesystem\Handlers
 */
class Ftp
{
    use ConfigCollectorTrait;
    use ErrorCollectorTrait;

    /**
     * Passive mode flag
     *
     * @var    bool
     */
    public $passiveMode = true;

    /**
     * Debug flag
     *
     * Specifies whether to display error messages.
     *
     * @var    bool
     */
    public $debugMode = false;

    /**
     * Ftp::$config
     *
     * Ftp configuration.
     *
     * @var array
     */
    protected $config;

    // --------------------------------------------------------------------
    /**
     * Connection ID
     *
     * @var    resource
     */
    protected $handle;

    // --------------------------------------------------------------------

    /**
     * Ftp::__construct
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        // Prep the port
        $this->config[ 'port' ] = empty($this->config[ 'port' ]) ? 21 : (int)$this->config[ 'port' ];

        // Prep the hostname
        $this->config[ 'hostname' ] = preg_replace('|.+?://|', '', $this->config[ 'hostname' ]);

        language()
            ->addFilePath(str_replace('Handlers', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('ftp');
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::connect
     *
     * Connect to FTP server.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws RuntimeException
     */
    public function connect()
    {
        if (false === ($this->handle = @ftp_connect($this->config[ 'hostname' ], $this->config[ 'port' ]))) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_CONNECT');
            }

            $this->addError(1, 'FTP_E_UNABLE_TO_CONNECT');

            return false;
        }

        if (false !== (@ftp_login($this->handle, $this->config[ 'username' ], $this->config[ 'password' ]))) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_LOGIN');
            }

            $this->addError(2, 'FTP_E_UNABLE_TO_LOGIN');

            return false;
        }

        // Set passive mode if needed
        if ($this->passiveMode === true) {
            ftp_pasv($this->handle, true);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::download
     *
     * Download a file from a remote server to the local server
     *
     * @param   string $remoteFilePath Remote file path.
     * @param   string $localFilePath  Local destination file path.
     * @param   string $mode           File transfer mode.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function download($remoteFilePath, $localFilePath, $mode = 'auto')
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        // Set the mode if not specified
        if ($mode === 'auto') {
            // Get the file extension so we can set the upload type
            $ext = $this->getExtension($remoteFilePath);
            $mode = $this->getTransferMode($ext);
        }

        $mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

        $result = @ftp_get($this->handle, $localFilePath, $remoteFilePath, $mode);

        if ($result === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_DOWNLOAD');
            }

            $this->addError(3, 'FTP_E_UNABLE_TO_DOWNLOAD');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::isConnected
     *
     * Validates the connection ID
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws RuntimeException
     */
    protected function isConnected()
    {
        if ( ! is_resource($this->handle)) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_NO_CONNECTION');
            }

            $this->addError(4, 'FTP_E_NO_CONNECTION');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::getExtension
     *
     * Extract the file extension.
     *
     * @param   string $filename String of filename to be extracted.
     *
     * @return  string By default it's set into txt file extension.
     */
    protected function getExtension($filename)
    {
        return (($dot = strrpos($filename, '.')) === false)
            ? 'txt'
            : substr($filename, $dot + 1);
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::getTransferMode
     *
     * Gets upload transfer mode.
     *
     * @param   string $ext Filename extension.
     *
     * @return  string By default it's set into ascii mode.
     */
    protected function getTransferMode($ext)
    {
        return in_array(
            $ext,
            ['txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml'],
            true
        )
            ? 'ascii'
            : 'binary';
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::rename
     *
     * Rename a file on ftp server.
     *
     * @param   string $oldFilename Old filename.
     * @param   string $newFilename New filename.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function rename($oldFilename, $newFilename)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        $result = @ftp_rename($this->handle, $oldFilename, $newFilename);

        if ($result === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_UNABLE_TO_RENAME');
            }

            $this->addError(5, 'FTP_UNABLE_TO_RENAME');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::moveFile
     *
     * Moves a file on the FTP server.
     *
     * @param    string $oldRemoteFilePath Old file path on the FTP server.
     * @param    string $newRemoteFilePath New file path on the FTP server.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function move($oldRemoteFilePath, $newRemoteFilePath)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        $result = @ftp_rename($this->handle, $oldRemoteFilePath, $newRemoteFilePath);

        if ($result === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_UNABLE_TO_MOVE');
            }

            $this->addError(6, 'FTP_UNABLE_TO_MOVE');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::deleteFile
     *
     * Deletes a file on the FTP server
     *
     * @param   string $filePath Path to the file to be deleted.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function deleteFile($filePath)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        $result = @ftp_delete($this->handle, $filePath);

        if ($result === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_DELETE');
            }

            $this->addError(7, 'FTP_E_UNABLE_TO_DELETE');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::deleteDir
     *
     * Delete a folder and recursively delete everything (including sub-folders)
     * contained within it on the FTP server.
     *
     * @param   string $remotePath Path to the directory to be deleted on the FTP server.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function deleteDir($remotePath)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        // Add a trailing slash to the file path if needed
        $remotePath = preg_replace('/(.+?)\/*$/', '\\1/', $remotePath);

        $list = $this->getFiles($remotePath);
        if ( ! empty($list)) {
            for ($i = 0, $c = count($list); $i < $c; $i++) {
                // If we can't delete the item it's probaly a directory,
                // so we'll recursively call delete_dir()
                if ( ! preg_match('#/\.\.?$#', $list[ $i ]) && ! @ftp_delete($this->handle, $list[ $i ])) {
                    $this->deleteDir($list[ $i ]);
                }
            }
        }

        if (@ftp_rmdir($this->handle, $remotePath) === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_DELETE_DIRECTORY');
            }

            $this->addError(8, 'FTP_E_UNABLE_TO_DELETE_DIRECTORY');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::getFiles
     *
     * FTP List files in the specified directory.
     *
     * @param    string $remotePath Path to the remote directory.
     *
     * @return  array|bool Returns array of files list or FALSE on failure.
     * @throws  RuntimeException
     */
    public function getFiles($remotePath = '.')
    {
        return $this->isConnected()
            ? ftp_nlist($this->handle, $remotePath)
            : false;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::mirror
     *
     * Read a directory and recreate it remotely.
     *
     * This function recursively reads a folder and everything it contains
     * (including sub-folders) and creates a mirror via FTP based on it.
     * Whatever the directory structure of the original file path will be
     * recreated on the server.
     *
     * @param    string $localPath  Path to source with trailing slash
     * @param    string $remotePath Path to destination - include the base folder with trailing slash
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function mirror($localPath, $remotePath)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        // Open the local file path
        if ($fp = @opendir($localPath)) {
            // Attempt to open the remote file path and try to create it, if it doesn't exist
            if ( ! $this->changeDir($remotePath, true) && ( ! $this->makeDir($remotePath) OR ! $this->changeDir(
                        $remotePath
                    ))
            ) {
                return false;
            }

            // Recursively read the local directory
            while (false !== ($file = readdir($fp))) {
                if (is_dir($localPath . $file) && $file[ 0 ] !== '.') {
                    $this->mirror($localPath . $file . '/', $remotePath . $file . '/');
                } elseif ($file[ 0 ] !== '.') {
                    // Get the file extension so we can se the upload type
                    $ext = $this->getExtension($file);
                    $mode = $this->getTransferMode($ext);

                    $this->upload($localPath . $file, $remotePath . $file, $mode);
                }
            }

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::changeDir
     *
     * The second parameter lets us momentarily turn off debugging so that
     * this function can be used to test for the existence of a folder
     * without throwing an error. There's no FTP equivalent to is_dir()
     * so we do it by trying to change to a particular directory.
     * Internally, this parameter is only used by the "mirror" function below.
     *
     * @param   string $remotePath    The remote directory path.
     * @param   bool   $suppressDebug Suppress debug mode.
     *
     * @return  bool  Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function changeDir($remotePath, $suppressDebug = false)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        $result = @ftp_chdir($this->handle, $remotePath);

        if ($result === false) {
            if ($this->debugMode === true AND $suppressDebug === false) {
                throw new RuntimeException('FTP_E_UNABLE_TO_CHANGE_DIRECTORY');
            }

            $this->addError(9, 'FTP_E_UNABLE_TO_CHANGE_DIRECTORY');

            return false;
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Ftp::makeDir
     *
     * Create a remote directory on the ftp server.
     *
     * @param   string $remotePath  The remote directory that will be created on ftp server.
     * @param   int    $permissions The remote directory permissions.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function makeDir($remotePath, $permissions = null)
    {
        if ($remotePath === '' OR ! $this->isConnected()) {
            return false;
        }

        $result = @ftp_mkdir($this->handle, $remotePath);

        if ($result === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_MAKE_DIRECTORY');
            }

            $this->addError(10, 'FTP_E_UNABLE_TO_MAKE_DIRECTORY');

            return false;
        }

        // Set file permissions if needed
        if ($permissions !== null) {
            $this->setChmod($remotePath, (int)$permissions);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::setChmod
     *
     * Set remote file permissions.
     *
     * @param   string $remotePath Path to the remote directory or file to be changed.
     * @param   int    $mode       Remote directory permissions mode.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function setChmod($remotePath, $mode)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        if (@ftp_chmod($this->handle, $mode, $remotePath) === false) {
            if ($this->debugMode === true) {
                throw new RuntimeException('FTP_E_UNABLE_TO_CHMOD');
            }

            $this->addError(11, 'FTP_E_UNABLE_TO_CHMOD');

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Ftp::upload
     *
     * Uploader a file to the ftp server.
     *
     * @param    string $localFilePath  Local source file path.
     * @param    string $remoteFilePath Remote destination file path.
     * @param    string $mode           File transfer mode.
     * @param    int    $permissions    Remote file permissions.
     *
     * @return  bool Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function upload($localFilePath, $remoteFilePath, $mode = 'auto', $permissions = null)
    {
        if ( ! $this->isConnected()) {
            return false;
        }

        if (is_file($localFilePath)) {
            // Set the mode if not specified
            if ($mode === 'auto') {
                // Get the file extension so we can set the upload type
                $ext = $this->getExtension($localFilePath);
                $mode = $this->getTransferMode($ext);
            }

            $mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

            $result = @ftp_put($this->handle, $remoteFilePath, $localFilePath, $mode);

            if ($result === false) {
                if ($this->debugMode === true) {
                    throw new RuntimeException('FTP_E_UNABLE_TO_UPLOAD');
                }

                $this->addError(12, 'FTP_E_UNABLE_TO_UPLOAD');

                return false;
            }

            // Set file permissions if needed
            if ($permissions !== null) {
                $this->setChmod($remoteFilePath, (int)$permissions);
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Ftp::close
     *
     * Close the current ftp connection.
     *
     * @return  bool    Returns TRUE on success or FALSE on failure.
     * @throws  RuntimeException
     */
    public function close()
    {
        return $this->isConnected()
            ? @ftp_close($this->handle)
            : false;
    }
}