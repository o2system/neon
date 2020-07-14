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

namespace O2System\Filesystem\Files;

// ------------------------------------------------------------------------

use O2System\Filesystem\Files\Abstracts\AbstractFile;

/**
 * Class ZipFile
 *
 * @package O2System\Filesystem\Files
 */
class ZipFile extends AbstractFile
{
    /**
     * XmlFile::readFile
     *
     * @param string $filePath Path to the file.
     * @param array  $options  Read file options.
     *
     * @return mixed
     */
    public function readFile($filePath = null, array $options = [])
    {
        $filePath = empty($filePath)
            ? $this->filePath
            : $filePath;


        $result = [];

        if (extension_loaded('zip')) {
            if ($zipHandle = zip_open($filePath)) {
                while ($zipContent = zip_read($zipHandle)) {
                    $result[] = zip_entry_name($zipContent);
                }

                zip_close($zipHandle);
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * ZipFile::writeFile
     *
     * @param string $filePath Path to the file.
     * @param array  $options  Write file options.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function writeFile($filePath = null, array $options = [])
    {
        $filePath = empty($filePath)
            ? $this->filePath
            : $filePath;

        if ($this->count()) {
            $zip = new \ZipArchive();

            if ($zip->open($filePath, \ZipArchive::CREATE)) {
                foreach ($this->getArrayCopy() as $directory => $files) {
                    if (is_array($files)) {
                        $zip->addEmptyDir($directory);
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                $zip->addFile($file);
                            }
                        }
                    } elseif (is_file($files)) {
                        $zip->addFile($files);
                    }
                }

                return $zip->close();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ZipFile::compress
     *
     * Zipped a folder (include itself).
     *
     * @param string      $sourcePath Path of directory to be zip.
     * @param string|null $filePath   Path to the zip file.
     */
    public function compress($sourcePath, $filePath = null)
    {
        $filePath = empty($filePath)
            ? $this->filePath
            : $filePath;

        $zip = new \ZipArchive();
        $zip->open($filePath, \ZipArchive::CREATE);
        $zip->addEmptyDir(pathinfo($sourcePath, PATHINFO_BASENAME));
        $this->recursiveCompress($sourcePath, strlen(dirname($sourcePath, 2) . DIRECTORY_SEPARATOR), $zip);
        $zip->close();
    }

    // ------------------------------------------------------------------------

    /**
     * ZipFile::recursiveCompress
     *
     * Add files and sub-directories in a folder to zip file.
     *
     * @param string      $directory       Path of directory to be zip.
     * @param int         $exclusiveLength Number of text to be exclusive from the file path.
     * @param \ZipArchive $zipArchive      Zip Archive instance.
     */
    private function recursiveCompress($directory, $exclusiveLength, &$zipArchive)
    {
        $handle = opendir($directory);

        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $filePath = "$directory/$file";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipArchive->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipArchive->addEmptyDir($localPath);
                    $this->recursiveCompress($filePath, $exclusiveLength, $zipArchive);
                }
            }
        }
        closedir($handle);
    }

    // ------------------------------------------------------------------------

    /**
     * ZipFile::extract
     *
     * Extract a zip file.
     *
     * @param string $destinationPath Path of unzip directory.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function extract($destinationPath)
    {
        if (extension_loaded('zip')) {
            if (is_file($this->filePath)) {
                $zip = new \ZipArchive;
                $contents = $zip->open($this->filePath);

                if ($contents === true) {
                    if (is_dir($destinationPath)) {
                        $zip->extractTo($destinationPath);
                        $zip->close();

                        return true;
                    }
                }
            }
        }

        return false;
    }
}