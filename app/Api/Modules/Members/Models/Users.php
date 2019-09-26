<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 14/09/2019
 * Time: 14:41
 */

namespace App\Api\Modules\Members\Models;


use O2System\Framework\Models\Sql\Model;

class Users extends Model
{
    public $table = 'members_users';

    /**
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */

    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member');
    }

    /**
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */

    public function user()
    {
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'id_sys_user');
    }

}