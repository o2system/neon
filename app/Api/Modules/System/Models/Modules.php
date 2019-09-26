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

use App\Api\Modules\System\Models\Modules\Menus;
use App\Api\Modules\System\Models\Modules\Roles;
use App\Api\Modules\System\Models\Modules\Segments;
use App\Api\Modules\System\Models\Modules\Settings;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;

/**
 * Class Modules
 * @package App\Api\Modules\System\Models
 */
class Modules extends Model
{
    use HierarchicalTrait;

    /**
     * Modules::$table
     *
     * @var string
     */
    public $table = 'sys_modules';

    // ------------------------------------------------------------------------

    /**
     * Modules::menus
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function menus()
    {
        return $this->hasMany(Menus::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::roles
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function roles()
    {
        return $this->hasMany(Roles::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::segments
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function segments()
    {
        return $this->hasMany(Segments::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::settings
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function settings()
    {
        return $this->hasMany(Settings::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::users
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function users()
    {
        return $this->hasMany(Users::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::parent
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function parent()
    {
        return $this->getParent($this->row->id_parent);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::parents
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function parents()
    {
        return $this->getParents($this->row->id_parent);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::childs
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function childs()
    {
        return $this->getChilds($this->row->id);
    }

    public function form($post)
    {
        $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';
        switch ($action) {
            case 'INSERT':
            unset($post->id);
            $post->record_create_user = $post->record_update_user = globals()->account->id;

            if (parent::insert($post->getArrayCopy())) {
                session()->setFlash('success', language('SUCCESS_INSERT'));
                return true;
            } else {
                session()->setFlash('danger', language('FAILED_INSERT'));
                return false;
            }
            break;

            case 'UPDATE':
            $post->record_update_user = globals()->account->id;

            if (parent::update($post->getArrayCopy())) {
                session()->setFlash('success', language('SUCCESS_UPDATE'));
                return true;
            } else {
                session()->setFlash('danger', language('FAILED_UPDATE'));
                return false;
            }
            break;
        }
    }

    public function addUser($post)
    {
        $users = $post->users;
        $dataUser = [];
        foreach ($users as $idSysUser => $user){
            $dataUser[$idSysUser]['id_sys_module_user'] = $idSysUser;
            $dataUser[$idSysUser]['permission'] = $user['permission'];
            $dataUser[$idSysUser]['id_sys_module_segment'] = input()->get('id_sys_module_segment');
        }
        if(is_array($dataUser)){
            foreach ($dataUser as $data){
                if(models(\App\Api\Modules\System\Models\Modules\Segments\Authorities\Users::class)->findWhere([
                    'id_sys_module_user' => $data['id_sys_module_user'],
                    'id_sys_module_segment' => $data['id_sys_module_segment']
                ])->count()){
                    models(\App\Api\Modules\System\Models\Modules\Segments\Authorities\Users::class)->update($data,[
                        'id_sys_module_user' => $data['id_sys_module_user'],
                        'id_sys_module_segment' => $data['id_sys_module_segment']
                    ]);
                }else{
                    models(\App\Api\Modules\System\Models\Modules\Segments\Authorities\Users::class)->insert($data);
                }
            }
        }
    }
}