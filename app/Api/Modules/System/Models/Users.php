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

namespace App\Api\Modules\System\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules\Users\Settings;
use App\Api\Modules\System\Models\Users\Profiles;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RecordTrait;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Users
 * @package App\Api\Modules\System\Models
 */
class Users extends Model
{
    use RelationTrait, RecordTrait;

    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_users';

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        if ($profile = $this->hasOne(Profiles::class, 'id_sys_user')) {
            $profile->name = $profile->name_display;
            $filePath = PATH_STORAGE . 'images/users/' . $profile->avatar;
            if (is_file($filePath)) {
                $profile->avatar = images_url($filePath);
            } else {
                $profile->avatar = images_url('default/avatar-' . strtolower($profile->gender) . '.jpg');
            }

            return $profile;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::role
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        return $this->belongsToThrough(Modules\Roles::class, Modules\Users::class,
            'id_sys_user', 'id_sys_module_role');

    }

    // ------------------------------------------------------------------------

    /**
     * Users::setting
     *
     * @return SplArrayObject|bool Returns FALSE if failed.
     */
    public function setting()
    {
        $settings = $this->hasMany(Settings::class, 'id_sys_user');
        if ($settings) {
            $metadata = new SplArrayObject();

            foreach ($settings as $row) {
                $metadata[ $row->key ] = $row->value;
            }

            return $metadata;
        }

        return false;
    }
}