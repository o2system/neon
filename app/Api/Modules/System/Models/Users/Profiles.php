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

namespace App\Api\Modules\System\Models\Users;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Users;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Profiles
 * @package AApp\Api\Modules\System\Models\Users
 */
class Profiles extends Model
{
    use RelationTrait;

    /**
     * Profile::$table
     *
     * @var string
     */
    public $table = 'sys_users_profiles';

    // ------------------------------------------------------------------------

    /**
     * Profiles::account
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function account()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }
}