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

namespace O2System\Gear\Trace\DataStructures;

// ------------------------------------------------------------------------

/**
 * O2System Gear Trace Chronology
 *
 * @package O2System\Gear\Trace
 */
class Chronology extends \ArrayObject
{
    /**
     * Chronology::__construct
     *
     * @param array $chronology
     */
    public function __construct(array $chronology = [])
    {
        $chronology = empty($chronology) ? [
            'call' => null,
            'type' => null,
            'line' => null,
            'time' => null,
            'args' => [],
            'file' => null,
        ] : $chronology;

        parent::__construct($chronology, \ArrayObject::ARRAY_AS_PROPS);
    }
}