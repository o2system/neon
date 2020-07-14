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

namespace O2System\Filesystem\Files\Abstracts;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class AbstractFile
 *
 * @package O2System\Filesystem\Files\Abstracts
 */
abstract class AbstractFile extends AbstractRepository
{
    /**
     * AbstractFile::$fileExtension
     *
     * @var string
     */
    protected $fileExtension;

    /**
     * AbstractFile::$filePath
     *
     * @var string
     */
    protected $filePath;

    // ------------------------------------------------------------------------

    /**
     * AbstractFile::__construct
     *
     * @param string|null $filePath Path to the file.
     */
    final public function __construct($filePath = null)
    {
        if (isset($filePath)) {
            $this->filePath = $filePath;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractFile::createFile
     *
     * @param string $filePath Path to the file.
     *
     * @return static
     */
    final public function createFile($filePath = null)
    {
        if (isset($filePath)) {
            $this->filePath = $filePath;
        }

        if (pathinfo($this->filePath, PATHINFO_EXTENSION) === '') {
            $this->filePath .= $this->fileExtension;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractFile::readFile
     *
     * @param string $filePath Path to the file.
     * @param array  $options  Read file options.
     *
     * @return mixed
     */
    abstract public function readFile($filePath = null, array $options = []);

    // ------------------------------------------------------------------------

    /**
     * AbstractFile::writeFile
     *
     * @param string $filePath Path to the file.
     * @param array  $options  Write file options.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    abstract public function writeFile($filePath = null, array $options = []);
}