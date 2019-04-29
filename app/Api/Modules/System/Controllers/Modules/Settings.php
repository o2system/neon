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

use App\Api\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Settings
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Settings extends Controller
{
    /**
     * Settings::index
     */
    public function index()
    {
        if ($get = input()->get()) {
            if (false !== ($result = Models\Modules\Settings::withPaging()->findWhere([
                    'id_sys_module' => $get->id_sys_module,
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