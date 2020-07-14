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

namespace O2System\Database\DataObjects\Result;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Info
 * @package O2System\Database\DataObjects\Result
 */
class Info extends SplArrayObject
{
    public function __construct()
    {
        parent::__construct([
            'num_per_page' => 0,
            'num_rows' => 0,
            'num_founds' => 0,
            'num_pages' => 0,
            'num_total' => 0,
            'numbering' => new SplArrayObject([
                'start' => 0,
                'end' => 0
            ])
        ]);
    }
}