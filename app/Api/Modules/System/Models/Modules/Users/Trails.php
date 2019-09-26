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

namespace App\Api\Modules\System\Models\Modules\Users;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Trails
 * @package App\Api\Modules\System\Models\Modules\Users
 */
class Trails extends Model
{
    /**
     * Trails::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_trails';

    /**
     * Trails::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_sys_module_user',
        'time_start',
        'time_end',
        'action_session',
        'action_ip',
        'action_type',
        'action_url',
        'action_status',
        'metadata'
    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Modules\Users::class, 'id_sys_module_user');
    }
}