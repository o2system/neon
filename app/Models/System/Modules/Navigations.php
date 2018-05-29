<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */

// ------------------------------------------------------------------------

namespace App\Models\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Navigations
 *
 * @package O2System\Framework\Sql\Models
 */
class Navigations extends Model
{
    public function getItems( $position = 'ALL' )
    {
        if( $position !== 'ALL' ) {
            $this->qb->where( [ 'sys_modules_navigations.position' => strtoupper( $position ) ] );
        }

        $result = $this->qb
            ->table( 'sys_modules_navigations' )
            ->select( [
                'sys_modules_navigations.*',
                'sys_modules_users_roles_access.permission',
                'sys_users_roles_access.permission AS user_permission'
            ] )
            ->join( 'sys_modules', 'sys_modules.id = sys_modules_navigations.id_sys_module' )
            ->join( 'sys_modules_users_roles_access', 'sys_modules_users_roles_access.segments = sys_modules_navigations.href', 'LEFT' )
            ->join( 'sys_users_roles_access', 'sys_users_roles_access.segments = sys_modules_navigations.href', 'LEFT' )
            ->where([
                'sys_modules_navigations.id_parent' => null,
                'sys_modules_navigations.record_status' => 'PUBLISH',
                'sys_modules.namespace' => modules()->current()->getNamespace()
            ])
            ->orderBy('sys_modules_navigations.record_ordering', 'ASC')
            ->get();

        if( $result->count() ) {
            return $result;
        }

        return false;
    }

    public function getSubItems( $id_parent )
    {
        $result = $this->qb
            ->table( 'sys_modules_navigations' )
            ->select( [
                'sys_modules_navigations.*',
                'sys_modules_users_roles_access.permission',
                'sys_users_roles_access.permission AS user_permission'
            ] )
            ->join( 'sys_modules', 'sys_modules.id = sys_modules_navigations.id_sys_module' )
            ->join( 'sys_modules_users_roles_access', 'sys_modules_users_roles_access.segments = sys_modules_navigations.href', 'LEFT' )
            ->join( 'sys_users_roles_access', 'sys_users_roles_access.segments = sys_modules_navigations.href', 'LEFT' )
            ->where([
                'sys_modules_navigations.id_parent' => $id_parent,
                'sys_modules_navigations.record_status' => 'PUBLISH',
                'sys_modules.namespace' => modules()->current()->getNamespace()
            ])
            ->orderBy('sys_modules_navigations.record_ordering', 'ASC')
            ->get();

        if( $result->count() ) {
            return $result;
        }

        return false;
    }
}