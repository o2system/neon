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

namespace O2System\Email;

// ------------------------------------------------------------------------

use O2System\Spl\Info\SplFileInfo;

/**
 * Class Attachment
 *
 * @package O2System\Email
 */
class Attachment extends SplFileInfo
{
    /**
     * Attachment::$filename
     *
     * filename of email attachment
     *
     * @var string
     */
    protected $filename;

    // ------------------------------------------------------------------------

    /**
     * Attachment::setFilename
     *
     * Set Filename email attachment
     *
     * @param string $filename
     *
     * @return  static
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }
}