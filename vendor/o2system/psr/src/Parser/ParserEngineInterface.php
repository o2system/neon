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
 * Interface ParserEngineInterface
 *
 * @package O2System\Psr\Parser
 */
interface ParserEngineInterface
{
    public function getFileExtensions();

    public function addFilePath($path);

    public function parseFile($filePath, array $vars = []);

    public function parseString($source, array $vars = []);
}