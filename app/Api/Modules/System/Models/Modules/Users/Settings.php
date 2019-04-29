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
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Settings
 * @package App\Api\Modules\System\Models\Modules\Users
 */
class Settings extends Model
{
    use RelationTrait;

    /**
     * Settings::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users_settings';

    // ------------------------------------------------------------------------

    /**
     * Settings::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_module_user');
    }
}