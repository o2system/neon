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

namespace O2System\Database\DataObjects\Result\Row\Columns;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class DataJSON
 *
 * @package O2System\DB\DataStructures\Row\Columns
 */
class DataJSON extends SplArrayObject
{
    /**
     * DataJSON::__construct
     *
     * SimpleJSONField constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct([]);

        if ( ! empty($data)) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * DataJSON::__set
     *
     * Magic Method __set
     *
     * @param string $index
     *
     * @param int    $value
     */
    public function __set($index, $value)
    {
        if (is_array($value)) {
            $value = new self($value);
        }

        $this->offsetSet($index, $value);
    }

    // ------------------------------------------------------------------------

    /**
     * DataJSON__toArray
     *
     * Magic Method __toArray
     *
     * @return array
     */
    public function __toArray()
    {
        return $this->getArrayCopy();
    }
}