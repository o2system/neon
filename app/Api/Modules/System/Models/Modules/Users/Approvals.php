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
 * Class Approvals
 * @package App\Api\Modules\System\Models\Modules\Users
 */
class Approvals extends Model
{
    /**
     * Approvals::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_approvals';

    /**
     * Approvals::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_sys_module_user',
        'role',
        'step',
        'reference_id',
        'reference_model',
    ];

    // ------------------------------------------------------------------------

    /**
     * Approvals::roleOptions
     *
     * @return array
     */
    public function roleOptions()
    {
        return [
            'ACKNOWLEDGE'    => language('ACKNOWLEDGE'),
            'ADMINISTRATION' => language('ADMINISTRATION'),
            'APPROVER'       => language('APPROVER'),
            'CERTIFIER'      => language('CERTIFIER'),
            'CLAIMER'        => language('CLAIMER'),
            'CONTRIVER'      => language('CONTRIVER'),
            'DONOR'          => language('DONOR'),
            'DEVISER'        => language('DEVISER'),
            'ENDOSER'        => language('ENDOSER'),
            'EXAMINER'       => language('EXAMINER'),
            'EXECUTOR'       => language('EXECUTOR'),
            'FINDER'         => language('FINDER'),
            'GIVER'          => language('GIVER'),
            'GRANTOR'        => language('GRANTOR'),
            'HANDOVERER'     => language('HANDOVERER'),
            'INDOSER'        => language('INDOSER'),
            'INFORMER'       => language('INFORMER'),
            'INSPECTOR'      => language('INSPECTOR'),
            'INVENTOR'       => language('INVENTOR'),
            'PRESENTER'      => language('PRESENTER'),
            'PREPARER'       => language('PREPARER'),
            'PROMOTOR'       => language('PROMOTOR'),
            'PROVIDER'       => language('PROVIDER'),
            'RECEIVER'       => language('RECEIVER'),
            'RECIPIENT'      => language('RECIPIENT'),
            'REPORTER'       => language('REPORTER'),
            'REQUESTER'      => language('REQUESTER'),
            'SUPERIOR'       => language('SUPERIOR'),
            'UPLOADER'       => language('UPLOADER'),
            'WRITER'         => language('WRITER'),
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * Approvals::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Modules\Users::class, 'id_sys_modules_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Approvals::reference
     *
     * @return bool|mixed|\O2System\Database\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function reference()
    {
        if ($result = models($this->row->reference_model)->find($this->row->reference_id)) {
            return $result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Approvals::logs
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function logs()
    {
        return $this->hasMany(Modules\Users\Approvals\Logs::class, 'id_sys_module_user_approval');
    }

    // ------------------------------------------------------------------------

    /**
     * Approvals::latestLog
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function latestLog()
    {
        $this->qb->orderBy('id', 'DESC');

        return $this->hasOne(Modules\Users\Approvals\Logs::class, 'id_sys_module_user_approval');
    }
}