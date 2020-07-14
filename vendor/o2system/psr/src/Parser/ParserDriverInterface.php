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

namespace O2System\Psr\Parser;

/**
 * Interface ParserDriverInterface
 *
 * @package O2System\Psr\Parser
 */
interface ParserDriverInterface
{
    /**
     * ParserDriverInterface::setEngine
     *
     * @param object $engine
     *
     * @return static|void
     */
    public function setEngine($engine);

    /**
     * ParserDriverInterface::getEngine
     *
     * @return object
     */
    public function &getEngine();

    /**
     * ParserDriverInterface::loadFile
     *
     * @param string $filePath
     *
     * @return mixed
     */
    public function loadFile($filePath);

    /**
     * ParserDriverInterface::loadString
     *
     * @param string $string
     *
     * @return static|mixed
     */
    public function loadString($string);

    public function parse(array $vars = []);

    public function isSupported();
}