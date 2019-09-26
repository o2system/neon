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

use App\Api\Modules\System\Models\Modules\Users;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Notifications
 * @package App\Api\Modules\System\Models\Modules\Users
 */
class Notifications extends Model
{
    /**
     * Notifications::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_notifications';

    /**
     * Notifications::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'sys_module_user_sender_id',
        'sys_module_user_recipient_id',
        'reference_id',
        'reference_model',
        'metadata',
        'status',
        'message'
    ];

    /**
     * Notifications::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'sender',
        'recipient',
        'message'
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::sender
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function sender()
    {
        return $this->belongsTo(Users::class, 'sys_module_user_sender_id');
    }

    // ------------------------------------------------------------------------

    /**
     * Notifications::recipient
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function recipient()
    {
        return $this->belongsTo(Users::class, 'sys_module_user_recipient_id');
    }

    // ------------------------------------------------------------------------

    /**
     * Notifications::reference
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
     * Notifications::message
     *
     * @return string
     */
//    public function message()
//    {
//        if(($message = language()->getLine($this->row->message)) !== $this->row->message) {
//            parser()->loadString($message);
//            return parser()->parse($this->row);
//        }
//
//        parser()->loadString($this->row->message);
//
//        return parser()->parse($this->row);
//    }
    public function send()
    {
        if($this->row->sys_module_user_sender_id == 0){
            return new SplArrayObject([
               'identity'   => new SplArrayObject([
                   'name'   => 'ADMINISTRATOR',
                   'image'   => storage_url(PATH_STORAGE.'images/default/avatar.jpg'),
               ])
            ]);
        }
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'sys_module_user_sender_id');
     }
}