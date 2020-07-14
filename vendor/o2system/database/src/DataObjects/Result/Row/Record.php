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

namespace O2System\Database\DataObjects\Result\Row;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result\Row\Record\Create;
use O2System\Database\DataObjects\Result\Row\Record\Update;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Record
 * @package O2System\Database\DataObjects\Result\Row
 */
class Record extends SplArrayObject
{
    /**
     * Records::__construct
     */
    public function __construct()
    {
        parent::__construct([
            'status' => null,
            'create' => new Create(),
            'update' => new Update()
        ]);
    }
}