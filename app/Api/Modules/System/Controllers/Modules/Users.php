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

namespace App\Api\Modules\System\Controllers\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;
use App\Api\Modules\System\Models;
use O2System\Framework\Models\Sql\DataObjects\Result;

/**
 * Class Users
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Users extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = new Models\Modules\Users();
    }

    public function sendPayload($data, $longPooling = false)
    {
        if($data instanceof Result) {
            foreach($data as $item) {
                $item->account = $item->account();
                $item->account->profile = $item->account->profile;
                $item->role = $item->role;
            }
        }

        parent::sendPayload($data, $longPooling);
    }

    public function create()
    {
        parent::create(); // TODO: Change the autogenerated stub
    }

    /**
     * Users::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_sys_user',
            'label'    => 'ID Sys User',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys User cannot be empty and must be integer!',
        ],
        [
            'field'    => 'id_sys_module_role',
            'label'    => 'ID Sys Module Role',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys Module Role cannot be empty and must be integer!',
        ],
        [
            'field'    => 'status',
            'label'    => 'Status',
            'rules'    => 'optional',
            'messages' => 'Status cannot be empty! Examples ACTIVE, INACTIVE, BLOCKED_BY_USER, BLOCKED_BY_SYSTEM, BANNED_BY_USER, BANNED_BY_SYSTEM',
        ],
    ];
}
