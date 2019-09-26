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
 * Class Approvals
 * @package App\Api\Modules\System\Controllers\Modules\Users
 */
class Approvals extends Controller
{
    /**
     * Approvals::index
     * @throws \Exception
     */
    public function index()
    {
        if (false !== ($result = Modules\Users\Approvals::withPaging()->findWhere([
                'id_sys_module_user' => session()->account->id_sys_module_user,
            ]))) {
            $this->sendPayload($result);
        } else {
            $this->sendError(204);
        }
    }
}