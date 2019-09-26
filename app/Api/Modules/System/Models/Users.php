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

use App\Api\Modules\System\Models\Users\Profiles;
use O2System\Framework\Models\Sql\Model;
use O2System\Security\Generators\Token;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Users
 * @package App\Api\Modules\System\Models
 */
class Users extends Model
{
    /**
     * Users::$table
     * @author bagus setiawan
     * @var string
     */
    public $table = 'sys_users';

    // ------------------------------------------------------------------------

    /**
     * Users::moduleUser
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function moduleUser()
    {
        return $this->hasOne(Modules\Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        if ($profile = $this->employee()) {
            return $profile;
        } elseif ($profile = $this->hasOne(Profiles::class, 'id_sys_user')) {
            $profile->name = $profile->name_full;
            $filePath = PATH_STORAGE . 'images/users/' . $profile->avatar;
            if (is_file($filePath)) {
                $profile->avatar = storage_url($filePath);
            } else {
                $profile->avatar = storage_url('images/default/avatar-' . strtolower($profile->gender) . '.jpg');
            }
            $profile->designation = new SplArrayObject([
                'title'      => language('NO_DESIGNATION'),
                'department' => new SplArrayObject([
                    'name' => language('NO_DEPARTMENT'),
                ]),
            ]);

            return $profile;
        }
        return false;
    }

    public function single_profile()
    {
        if ($data = $this->hasOne(Profiles::class, 'id_sys_user')) {
            return $data;
        }
        return false;
    }

    public function member()
    {
        if($memberUser = $this->hasOne(\App\Api\Modules\Members\Models\Users::class, 'id_sys_user')){
            return $memberUser->member;
        }

        return false;
    }
    public function identity()
    {
        if($this->profile()){
            $profile = $this->profile();
        }
        if($this->member()){
            $profile = $this->member();
        };
        if($this->company()){
            $profile = $this->company();
        };
        return $profile;
    }

    public function company()
    {
        if($companyUser = $this->hasOne(\App\Api\Modules\Companies\Models\Users::class, 'id_sys_user')){
            return $companyUser->company;
        }
        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::role
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->moduleUser()->role->module;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::role
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        return $this->moduleUser()->role;
    }

    // ------------------------------------------------------------------------

    /**
     * @param $field
     * @param $param
     *
     * @return bool
     */
    public function isExist($field, $param)
    {
        if (count($this->findWhere([
            $field => $param,
        ]))) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function userInsert($param, $roles = null, $profiles)
    {
        if ($param[ 'password' ] != $param[ 'password_confirm' ]) {
            return ['status' => 'danger', 'message' => language()->getLine('ALERT_PASSWORD_MISMATCH')];
        }

        // Is email exist.
        if ($this->isExist('email', $param[ 'email' ])) {
            return ['status' => 'danger', 'message' => language()->getLine('ALERT_EMAIL_EXIST')];
        }

        // Is username exist.
        if ($this->isExist('username', $param[ 'username' ])) {
            return ['status' => 'danger', 'message' => language()->getLine('ALERT_USERNAME_EXIST')];
        }

        $param[ 'pin' ] = $token = (new Token())->generate(6, 4);
        $param[ 'sso' ] = 123345;
        if ($param['msisdn'] == null) {
            $param['msisdn'] = $param['phone'];
        }
        unset($param[ 'password_confirm' ], $param['phone']);

        // Password mutation.
        $param[ 'password' ] = password_hash($param[ 'password' ], PASSWORD_DEFAULT);
        if ($this->insert($param)) {
            $idSysUser = $this->getLastInsertId();
            $roles[ 'id_sys_user' ] = $idSysUser;
            $roles[ 'id_sys_module_role' ] = 1;
            $profiles[ 'id_sys_user' ] = $idSysUser;
            if (false == models(Modules\Users::class)->insert($roles)) {
                $this->delete($id_sys_user);
                return ['status' => 'danger', 'message' => language()->getLine('FAILED_INSERT_ROLES')];
            }
            $profiles[ 'fullname' ] = $profiles[ 'name' ];
            unset($profiles[ 'name' ]);
            if (false == models(Profiles::class)->insert($profiles)) {
                $this->delete($id_sys_user);
                return ['status' => 'danger', 'message' => language()->getLine('FAILED_INSERT_PROFILE')];
            }

            return ['status' => 'success', 'message' => language()->getLine('ALERT_SUCCESS_INSERT'), 'id_sys_user' => $idSysUser, 'token' => $param[ 'pin' ]];
        } else {
            return ['status' => 'danger', 'message' => language()->getLine('FAILED_INSERT_USER')];
        }

    }

    // ------------------------------------------------------------------------

    public function userUpdate($param, $roles = null, $profiles)
    {
        if ($param[ 'password' ] != $param[ 'password_confirm' ]) {
            return ['status' => 'danger', 'message' => language()->getLine('ALERT_PASSWORD_MISMATCH')];
        }
        unset($param[ 'password_confirm' ]);
        $param[ 'pin' ] = $token = (new Token())->generate(10, 2);
        $param[ 'sso' ] = 123345;

        // Password mutation.
        if ($param[ 'password' ] == null) {
            unset($param[ 'password' ]);
        } else {
            $param[ 'password' ] = password_hash($param[ 'password' ], PASSWORD_DEFAULT);
        }
        
        if ($this->update($param)) {
            $idSysUser = $param[ 'id' ];

            if ($roles) {
                if (false == models(Modules\Users::class)->update($roles, ['id_sys_user' => $id_sys_user])) {
                    return ['status' => 'danger', 'message' => language()->getLine('FAILED_UPDATE_ROLES')];
                }
            }

            $profiles[ 'fullname' ] = $profiles[ 'name' ];
            unset($profiles[ 'name' ]);
            if (false == models(Profiles::class)->update($profiles, ['id_sys_user' => $idSysUser])) {
                return ['status' => 'danger', 'message' => language()->getLine('FAILED_UPDATE_PROFILE')];
            }

            return ['status' => 'success', 'message' => language()->getLine('ALERT_SUCCESS_INSERT')];
        } else {
            return ['danger' => 'success', 'message' => language()->getLine('FAILED_INSERT_USER')];
        }

    }

    public function delete($id)
    {
        if ($data = models(Profiles::class)->find($id, 'id_sys_user')) {
            if ($image = $data->avatar) {
                $imagePath = PATH_STORAGE . 'images/users/';
                if (file_exists($imagePath . $image)) {
                    unlink($imagePath . $image);
                }
            }
        }
        models(Profiles::class)->deleteManyBy(['id_sys_user' => $id]);
        models(Modules\Users::class)->deleteManyBy(['id_sys_user' => $id]);
        if (parent::delete($id)) {
            return true;
        }
        return false;
    }
}
