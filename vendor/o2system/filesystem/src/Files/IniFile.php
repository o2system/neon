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

/**
 * Class IniFile
 *
 * @package O2System\Filesystem\Factory
 */
class IniFile extends AbstractFile
{
    protected $fileExtension = '.ini';

    /**
     * IniFile::readFile
     *
     * @param string $filePath Path to the file.
     * @param array  $options  Read file options.
     *
     * @return mixed
     */
    public function readFile($filePath = null, array $options = [])
    {
        $items = parse_ini_file(
            $filePath,
            (empty($options)
                ? true
                : $options[ 'sections' ])
        );

        $this->merge($items);

        return $items;
    }

    // ------------------------------------------------------------------------

    /**
     * IniFile::writeFile
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

        $sections = (empty($options)
            ? true
            : $options[ 'sections' ]);

        $content = null;

        if ($sections) {
            foreach ($this->getArrayCopy() as $section => $data) {
                if (is_array($data)) {
                    $content .= '[' . $section . ']' . PHP_EOL;

                    foreach ($data as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $valueChild) {
                                $content .= $key . '[] = ' . (is_numeric($valueChild)
                                        ? $valueChild
                                        : '"' . $valueChild . '"') . PHP_EOL;
                            }
                        } elseif (strpos($key, ';') !== false) {
                            $content .= '; ' . trim(ucfirst($key), ';') . ' ' . $value . PHP_EOL;
                        } elseif (empty($value)) {
                            $content .= $key . ' = ' . PHP_EOL;
                        } else {
                            $content .= $key . ' = ' . (is_numeric($value)
                                    ? $value
                                    : '"' . $value . '"') . PHP_EOL;
                        }
                    }

                    $content .= PHP_EOL;
                } elseif (strpos($section, ';') !== false) {
                    $content .= '; ' . trim(ucfirst($section), ';') . ' ' . $data . PHP_EOL;
                } elseif (empty($data)) {
                    $content .= $section . ' = ' . PHP_EOL;
                } else {
                    $content .= $section . ' = ' . (is_numeric($data)
                            ? $data
                            : '"' . $data . '"') . PHP_EOL;
                }
            }
        } else {
            foreach ($this->getArrayCopy() as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $valueChild) {
                        $content .= $key . '[] = ' . (is_numeric($valueChild)
                                ? $valueChild
                                : '"' . $valueChild . '"') . PHP_EOL;
                    }
                } elseif (strpos($key, ';') !== false) {
                    $content .= '; ' . trim(ucfirst($key), ';') . ' ' . $value . PHP_EOL;
                } elseif (empty($value)) {
                    $content .= $key . ' = ' . PHP_EOL;
                } else {
                    $content .= $key . ' = ' . (is_numeric($value)
                            ? $value
                            : '"' . $value . '"') . PHP_EOL;
                }
            }
        }

        return (new File())->write($filePath, $content);
    }
}