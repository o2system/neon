<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\System\Models\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RecordTrait;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Roles
 * @package App\Api\Modules\System\Models\Users
 */
class Roles extends Model
{
    use RecordTrait, RelationTrait;

    /**
     * @var string
     */
    public $table = 'sys_users_roles';
}