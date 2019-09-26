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

namespace App\Api\Modules\System\Controllers\Modules\Users;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;
use App\Api\Modules\System\Models\Modules;

/**
 * Class Notifications
 * @package App\Api\Modules\System\Controllers\Modules\Users
 */
class Notifications extends Controller
{
    /**
     * Notifications::index
     * @throws \Exception
     */
    public function index()
    {
        $conditions = [
            'id_sys_module_user' => session()->account->id_sys_module_user,
        ];

        if ($get = input()->get('status')) {
            $conditions[ 'status' ] = $get->status;
        }

        if (false !== ($result = Modules\Users\Notifications::withPaging()->findWhere($conditions))) {
            $this->sendPayload($result);
        } else {
            $this->sendError(204);
        }
    }
}