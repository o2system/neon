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

namespace O2System\Psr\Loader;

// ------------------------------------------------------------------------

/**
 * Interface AutoloadInterface
 *
 * Describes a autoloader instance based on PSR-4 autoloader
 *
 * @see     http://www.php-fig.org/psr/psr-4/
 *
 * @package O2System\Psr\Loader
 */
interface AutoloadInterface
{
    /**
     * AutoloadInterface::register
     *
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register();

    // ------------------------------------------------------------------------

    /**
     * AutoloadInterface::addNamespace
     *
     * Adds a base directory for a namespace prefix.
     *
     * @param string $namespace     The namespace prefix.
     * @param string $baseDirectory A base directory for class files in the
     *                              namespace.
     * @param bool   $prepend       If true, prepend the base directory to the stack
     *                              instead of appending it; this causes it to be searched first rather
     *                              than last.
     *
     * @return void
     */
    public function addNamespace($namespace, $baseDirectory, $prepend = false);

    // ------------------------------------------------------------------------

    /**
     * AutoloadInterface::loadClass
     *
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class);

    // ------------------------------------------------------------------------

    /**
     * loadMappedFile::loadMappedFile
     *
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $namespace     The namespace prefix.
     * @param string $relativeClass The relative class name.
     *
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    public function loadMappedFile($namespace, $relativeClass);

    // ------------------------------------------------------------------------

    /**
     * loadMappedFile::requireFile
     *
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     *
     * @return bool True if the file exists, false if not.
     */
    public function requireFile($file);
}