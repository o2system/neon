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

use App\Api\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Notifications
 * @package App\Api\Modules\System\Controllers\Modules\Users
 */
class Notifications extends Controller
{
    /**
     * Notifications::index
     */
    public function index()
    {
        if ($get = input()->get()) {
            if (false !== ($result = Models\Modules\Users\Notifications::withPaging()->findWhere([
                    'id_sys_module'      => $get->id_sys_module,
                    'id_sys_module_user' => session()->account->id,
                    'seen'               => $get->offsetExists('seen') ? $get->seen : 'NO',
                ]))) {
                $this->sendPayload($result);
            } else {
                $this->sendError(204);
            }
        } else {
            $this->sendError(403);
        }
    }
}