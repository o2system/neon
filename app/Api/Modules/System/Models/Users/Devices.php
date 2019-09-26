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

use App\Api\Modules\System\Models\Users;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Devices
 * @package App\Api\Modules\System\Models\Users
 */
class Devices extends Model
{
    /**
     * Devices::$table
     *
     * @var string
     */
    public $table = 'sys_users_devices';

    public $visibleColumns = [
        'id',
        'timestamp',
        'useragent',
        'metadata'
    ];

    // ------------------------------------------------------------------------

    /**
     * Devices::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }
}