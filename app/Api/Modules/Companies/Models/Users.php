<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Companies\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Members\Models\Members;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Users
 * @package App\Api\Modules\Companies\Models
 */
class Users extends Model
{
    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'companies_users';

    /**
     * Users::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_company',
        'id_sys_user',
    ];

    /**
     * Users::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'company',
        'sys_user'
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::company
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function company()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }

    public function user(){
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'id_sys_user');
    }

//    public function module_user(){
//        return $this->sys_user->moduleUser;
//    }
}
