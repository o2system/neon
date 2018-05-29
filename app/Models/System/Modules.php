<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */
// ------------------------------------------------------------------------

namespace App\Models\System;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Modules
 *
 * @package O2System\Framework\Sql\Models
 */
class Modules extends Model
{
    /**
     * Modules::$table
     *
     * System modules database table name.
     *
     * @var string
     */
    public $table = 'sys_modules';
}