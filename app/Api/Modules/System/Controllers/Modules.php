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

namespace App\Api\Modules\System\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Modules
 * @package App\Api\Modules\System\Controllers
 */
class Modules extends Controller
{

    public function addRole()
    {
        if($post = input()->post()){
            if(models(Models\Modules\Segments\Authorities\Roles::class)->findWhere([
                'id_sys_module_segment'   => $post->id_sys_user_segment,
                'id_sys_module_role'    => $post->id_sys_module_role
            ])->count()){
                models(Models\Modules\Segments\Authorities\Roles::class)->update([
                    'id_sys_module_segment'   => $post->id_sys_user_segment,
                    'id_sys_module_role'    => $post->id_sys_module_role,
                    'permission'    => $post->permission
                ],[
                    'id_sys_module_segment'   => $post->id_sys_user_segment,
                    'id_sys_module_role'    => $post->id_sys_module_role
                ]);
            }else{
                models(Models\Modules\Segments\Authorities\Roles::class)->insert([
                    'id_sys_module_segment'   => $post->id_sys_user_segment,
                    'id_sys_module_role'    => $post->id_sys_module_role,
                    'permission'    => $post->permission
                ]);
            }
        }
    }
    public function addUser()
    {
        if($post = input()->post()){
            if(models(Models\Modules\Segments\Authorities\Users::class)->findWhere([
                'id_sys_module_segment'   => $post->id_sys_module_segment,
                'id_sys_module_user'    => $post->id_sys_module_user
            ])->count()){
                models(Models\Modules\Segments\Authorities\Users::class)->update([
                    'id_sys_module_segment'   => $post->id_sys_module_segment,
                    'id_sys_module_user'    => $post->id_sys_module_user,
                    'permission'    => $post->permission
                ],[
                    'id_sys_module_segment'   => $post->id_sys_module_segment,
                    'id_sys_module_user'    => $post->id_sys_module_user
                ]);
            }else{
                models(Models\Modules\Segments\Authorities\Users::class)->insert([
                    'id_sys_module_segment'   => $post->id_sys_module_segment,
                    'id_sys_module_user'    => $post->id_sys_module_user,
                    'permission'    => $post->permission
                ]);
            }
        }
    }
}