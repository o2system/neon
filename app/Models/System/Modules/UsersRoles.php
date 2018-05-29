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

namespace App\Models\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class UsersRoles
 *
 * @package O2System\Framework\Sql\Models\Modules
 */
class UsersRoles extends Model
{
    /**
     * UsersRoles::$table
     *
     * System modules users roles database table name.
     *
     * @var string
     */
    public $table = 'sys_modules_users_roles';
}