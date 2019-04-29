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
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Users
 * @package App\Api\Modules\System\Models\Modules
 */
class Users extends Model
{
    use RelationTrait;

    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_modules_users';

    // ------------------------------------------------------------------------

    /**
     * Users::getAccount
     *
     * @param array $conditions
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     * @throws \O2System\Psr\Cache\InvalidArgumentException
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function findWhere(array $conditions, $limit = 1)
    {
        $this->qb->select('sys_modules_users.*');
        $this->qb->join('sys_users', 'sys_users.id = sys_modules_users.id_sys_user');

        foreach($conditions as $field => $value) {
            $this->qb->where('sys_users.' . $field, $value);
        }

        if ($result = $this->qb
            ->from($this->table)
            ->get(1)) {
            if ($result->count() > 0) {
                $this->result = new Result($result, $this);
                $this->result->setInfo($result->getInfo());

                if ($this->result->count() == 1) {
                    return $this->result->first();
                }

                return $this->result;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::account
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function account()
    {
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        return $this->account()->profile;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class, 'id_sys_module');
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
}