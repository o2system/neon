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

namespace App\Api\Modules\System\Models\Modules\Users\Approvals;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules\Users\Approvals;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Logs
 * @package App\Api\Modules\System\Models\Modules\Users\Approvals
 */
class Logs extends Model
{
    /**
     * Logs::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_approvals_logs';

    /**
     * Logs::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_sys_module_user_approval',
        'timestamp',
        'status',
        'note',
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::approval
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function approval()
    {
        return $this->belongsTo(Approvals::class, 'id_sys_module_user_approval');
    }
}