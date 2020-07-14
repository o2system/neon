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

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Create
 * @package O2System\Database\DataObjects\Result\Row\Record
 */
class Create extends SplArrayObject
{
    /**
     * Create::__construct
     */
    public function __construct()
    {
        parent::__construct([
            'user'      => null,
            'timestamp' => null,
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Create::offsetSet
     *
     * @param string $index
     * @param mixed  $value
     *
     * @throws \Exception
     */
    public function offsetSet($index, $value)
    {
        if ($index === 'timestamp') {
            $value = new Timestamp($value);
        }

        parent::offsetSet($index, $value);
    }
}