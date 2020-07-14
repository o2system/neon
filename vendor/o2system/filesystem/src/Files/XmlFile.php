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

use O2System\Filesystem\File;
use O2System\Filesystem\Files\Abstracts\AbstractFile;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class XmlFile
 *
 * @package O2System\Filesystem\Factory
 */
class XmlFile extends AbstractFile
{
    protected $fileExtension = '.xml';

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

        $result = new ArrayIterator();

        if (false !== ($xml = simplexml_load_file($filePath))) {
            $contents = json_decode(json_encode($xml), true); // force to array conversion

            if (count($contents) == 1) {
                $contents = reset($contents);
            }

            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($contents as $content) {
                    $result[] = new SplArrayObject($content);
                }
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * XmlFile::writeFile
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
            $root = '<' . pathinfo($filePath, PATHINFO_FILENAME) . '/>';

            $contents = $this->getArrayCopy();
            $xml = new \SimpleXMLElement($root);
            array_walk_recursive($contents, [&$xml, 'addChild']);

            return (new File())->write($filePath, $xml->asXML());
        }
    }
}