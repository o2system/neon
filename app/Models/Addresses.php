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

namespace App\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Relationships;
use O2System\Framework\Models\Sql\System\Users;
use App\Models\Master\Geodirectories;

/**
 * Class Addresses
 * @package App\Models
 */
class Addresses extends Model
{
    /**
     * Addresses::$table
     *
     * @var string
     */
    public $table = 'addresses';

    /**
     * Addresses::geodirectory
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function geodirectory()
    {
        return $this->belongsTo(Geodirectories::class);
    }

    // ------------------------------------------------------------------------
}
