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

namespace O2System\Database\DataObjects\Result\Row\Record;

// ------------------------------------------------------------------------

/**
 * Class Timestamp
 * @package O2System\Database\DataObjects\Result\Row\Record
 */
class Timestamp extends \DateTime
{
    /**
     * Timestamp::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return date('Y-m-d H:i:s', $this->getTimestamp());
    }
}