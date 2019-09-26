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

namespace App\Api\Modules\System\Models\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules;
use O2System\Framework\Http\Message\ServerRequest;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Users
 * @package App\Api\Modules\System\Models\Modules
 */
class Users extends Model
{
    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users';

    /**
     * Users::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_sys_user',
        'id_sys_module_role',
        'status',
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::statusOptions
     *
     * @return array
     */
    public function statusOptions()
    {
        return [
            'ACTIVE'            => language('ACTIVE'),
            'INACTIVE'          => language('INACTIVE'),
            'BLOCKED_BY_USER'   => language('BLOCKED_BY_USER'),
            'BLOCKED_BY_SYSTEM' => language('BLOCKED_BY_SYSTEM'),
            'BANNED_BY_USER'    => language('BANNED_BY_USER'),
            'BANNED_BY_SYSTEM'  => language('BANNED_BY_SYSTEM'),
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * Users::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::role
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'id_sys_module_role');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::permission
     *
     * @param mixed $request
     *
     * @return string Returns DENIED | GRANTED | WRITE
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function permission($request = null)
    {
        $permission = 'DENIED';
        $uriString = server_request()->getUri()->getSegments()->__toString();

        if (isset($request)) {
            if ($request instanceof ServerRequest) {
                $uriString = $request->getUri()->getSegments()->__toString();
            } elseif (is_string($request)) {
                if (strpos($request, 'http') !== false) {
                    $url = parse_url($request);
                    $uriString = $url[ 'path' ];
                } elseif (strpos($request, '/') !== false) {
                    $uriString = $request;
                }
            } elseif (is_array($request)) {
                $uriString = implode('/', $request);
            }
        }

        $sysModulesSegmentsTable = models(Segments::class)->table;
        $sysModulesSegmentsAuthoritiesRolesTable = models(Modules\Segments\Authorities\Roles::class)->table;

        /**
         * Authorities by Role
         */
        if ($result = $this->qb
            ->select($sysModulesSegmentsAuthoritiesRolesTable . '.permission')
            ->from($sysModulesSegmentsAuthoritiesRolesTable)
            ->join($sysModulesSegmentsTable,
                $sysModulesSegmentsTable . '.id = ' . $sysModulesSegmentsAuthoritiesRolesTable . '.id_sys_module_segment')
            ->where([
                'id_sys_module_role'              => $this->row->id,
                $sysModulesSegmentsTable . '.uri' => $uriString,
            ])
            ->get(1)) {
            if ($result->count() == 1) {
                $permission = strtoupper($result->first()->permission);
            }
        }

        $sysModulesSegmentsAuthoritiesUsersTable = models(Modules\Segments\Authorities\Users::class)->table;

        /**
         * Authorities by User
         */
        if ($result = $this->qb
            ->select($sysModulesSegmentsAuthoritiesUsersTable . '.permission')
            ->from($sysModulesSegmentsAuthoritiesUsersTable)
            ->join($sysModulesSegmentsTable,
                $sysModulesSegmentsTable . '.id = ' . $sysModulesSegmentsAuthoritiesUsersTable . '.id_sys_module_segment')
            ->where([
                'id_sys_module_user'              => $this->row->id,
                $sysModulesSegmentsTable . '.uri' => $uriString,
            ])
            ->get(1)) {
            if ($result->count() == 1) {
                $permission = strtoupper($result->first()->permission);
            }
        }

        /**
         * By Default User Role
         */
        if (in_array(strtoupper($this->role()->code), ['DEVELOPER', 'ADMINISTRATOR'])) {
            $permission = 'WRITE';
        }

        return $permission;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        return $this->user()->profile;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::settings
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function settings()
    {
        if ($result = $this->hasMany(Modules\Users\Settings::class, 'id_post')) {
            $setting = new SplArrayObject();
            foreach ($result as $row) {
                $setting->offsetSet($row->key, $row->value);
            }

            return $setting;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::approvals
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function approvals()
    {
        return $this->hasMany(Modules\Users\Approvals::class, 'id_sys_module_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::notifies
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function notifies()
    {
        return $this->hasMany(Modules\Users\Notifications::class, 'module_user_sender_id');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::notifications
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function notifications()
    {
        return $this->hasMany(Modules\Users\Notifications::class, 'module_user_recipient_id');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::trails
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function trails()
    {
        return $this->hasMany(Modules\Users\Notifications::class, 'id_sys_module_user');
    }
}