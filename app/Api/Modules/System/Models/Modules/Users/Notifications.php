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

use App\Api\Modules\HumanResource\Models\Employee\Leaves;
use App\Api\Modules\Personal\Models\Leaves\Approvals;
use App\Api\Modules\System\Models\Modules\Users;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RecordTrait;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Notifications
 * @package App\Api\Modules\System\Models\Modules\Users
 */
class Notifications extends Model
{
    use RelationTrait, RecordTrait;

    /**
     * Notifications::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_notifications';

    // ------------------------------------------------------------------------

    /**
     * Authorities::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_module_user');
    }

    public function sender()
    {
        $sender = models(\App\Api\Modules\HumanResource\Models\Employee\Users::class)->findWhere([
           'id_sys_user'=> $this->row->id_sys_user_sender,
        ]);
        if(!count($sender)){
            new SplArrayObject([
               'id' => 0,
               'name'   => 'NO_USER',
               'employee' => new SplArrayObject([
                   'id' => 0,
                   'name'   => 'NO_EMPLOYEE'
               ])
            ]);
        }
        return $sender->first();
    }
    public function recipient()
    {
        $recipient = $this->belongsTo(Users::class, 'id_sys_user_recipient');
        if($recipient){
            new SplArrayObject([
                'id' => 0,
                'name'   => 'NO_USER',
                'employee' => new SplArrayObject([
                    'id' => 0,
                    'name'   => 'NO_EMPLOYEE'
                ])
            ]);
        }
        return $recipient;
    }
    public  function reference()
    {
        if($this->row->type == 'leaves' || $this->row->type == 'leaves_feed'){
            return $this->belongsTo(Leaves::class, 'id_reference');
        }
    }
    public function readNotifications($idSysUser)
    {
       return $this->findWhere([
           'id_sys_user_recipient'  => $idSysUser
       ]);
    }
}