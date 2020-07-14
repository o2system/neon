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

use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Directory
 *
 * @package O2System\Filesystem
 */
class Directory extends SplDirectoryInfo
{
    /**
     * Directory::make
     *
     * Make a directory.
     *
     * @param string $dir       Directory real path.
     * @param int    $mode      Directory mode.
     * @param bool   $recursive Make directory creation recursive.
     *
     * @return bool|\O2System\Spl\Info\SplDirectoryInfo
     */
    public function make($dir = null, $mode = 0777, $recursive = true)
    {
        $dir = is_null($dir) ? $this->getPathName() : $dir;

        if (is_dir($dir)) {
            return new SplDirectoryInfo($dir);
        } elseif (null !== ($pathName = $this->getPathName())) {
            if (mkdir(
                $makeDirectory = $pathName . DIRECTORY_SEPARATOR . str_replace(
                        ['\\', '/'],
                        DIRECTORY_SEPARATOR,
                        $dir
                    ),
                $mode,
                $recursive
            )) {
                return new SplDirectoryInfo($makeDirectory);
            }
        } elseif (mkdir(
            $makeDirectory = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir),
            $mode,
            $recursive
        )) {
            return new SplDirectoryInfo($makeDirectory);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Directory::delete
     *
     * Remove a directory.
     *
     * @param bool $fileOnly Remove files only and keep the directory structure.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function delete($fileOnly = false)
    {
        return $this->recursiveDelete($this->getRealPath(), $fileOnly);
    }

    // ------------------------------------------------------------------------

    /**
     * Directory::recursiveDelete
     *
     * @param string $dir      Directory path.
     * @param bool   $fileOnly Remove files only and keep the directory structure.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    private function recursiveDelete($dir, $fileOnly = false)
    {
        $dir = realpath($dir);

        if (is_dir($dir)) {
            $iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    $this->recursiveDelete($file->getRealPath(), $fileOnly);
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }

            if ($fileOnly === false) {
                rmdir($dir);
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Directory::setGroup
     *
     * Attempts to change the group of the directory to group.
     *
     * Only the superuser may change the group of a directory arbitrarily; other users may change the group of a file
     * to any group of which that user is a member.
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
     * Directory::setMode
     *
     * Attempts to change the mode of the specified file to that given in mode.
     *
     * @param int $mode The mode parameter consists of three octal number components specifying access restrictions for
     *                  the owner, the user group in which the owner is in, and to everybody else in this order. One
     *                  component can be computed by adding up the needed permissions for that target user base. Number
     *                  1 means that you grant execute rights, number 2 means that you make the directory writable,
     *                  number
     *                  4 means that you make the directory readable. Add up these numbers to specify needed rights.
     *                  You can also read more about modes on Unix systems with 'man 1 chmod' and 'man 2 chmod'.
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
     * Directory::setOwner
     *
     * Attempts to change the owner of the directory to user user.
     * Only the superuser may change the owner of a directory.
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
     * Directory::findFilesByExtension
     *
     * Find files by extension.
     *
     * @param string $extension
     *
     * @return array
     */
    public function findFilesByExtension($extension)
    {
        $extension = trim($extension, '.');

        $directoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getRealPath()),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $result = [];

        foreach ($directoryIterator as $directoryFile) {
            if ($directoryFile->isFile()) {
                if ($extension === '*') {
                    array_push($result, $directoryFile->getFilename());
                } elseif (preg_match('/\.' . $extension . '$/ui', $directoryFile->getFilename())) {
                    if ( ! in_array($directoryFile->getRealPath(), $result)) {
                        array_push($result, $directoryFile->getRealPath());
                    }
                }
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Directory::findFilesByFilename
     *
     * Find Files By Filename
     *
     * @param  string $filename
     *
     * @return array
     */
    public function findFilesByFilename($filename)
    {
        $directoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getRealPath()),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $result = [];

        foreach ($directoryIterator as $directoryFile) {
            if ($directoryFile->isFile()) {
                if (preg_match('/\\' . $filename . '.*/ui', $directoryFile->getFilename()) OR
                    preg_match('/\\' . ucfirst($filename) . '.*/ui', $directoryFile->getFilename())
                ) {
                    if ( ! in_array($directoryFile->getRealPath(), $result)) {
                        array_push($result, $directoryFile->getRealPath());
                    }
                }
            }
        }

        return $result;
    }
}

