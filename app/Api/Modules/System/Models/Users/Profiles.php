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
use App\Libraries\Rajaongkir;

/**
 * Class Profiles
 * @package AApp\Api\Modules\System\Models\Users
 */
class Profiles extends Model
{
    /**
     * Profile::$table
     *
     * @var string
     */
    public $table = 'sys_users_profiles';

    /**
     * Profile::$visibleColumns
     *
     * @var array
     */

    // ------------------------------------------------------------------------

    /**
     * Profiles::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }

    public function citizen()
    {
        $filePath = PATH_STORAGE . 'images/users/' . $this->row->citizen;
        if (is_file($filePath)) {
            return storage_url($filePath);
        }

        return storage_url('images/default/no-image.jpg');
    }

    public function taxpayer()
    {
        $filePath = PATH_STORAGE . 'images/users/' . $this->row->taxpayer;
        if (is_file($filePath)) {
            return storage_url($filePath);
        }

        return storage_url('images/default/no-image.jpg');
    }

    public function city()
    {
        if ($this->row->nonsibling_id_geodirectory) {
            $rajaongkir = new Rajaongkir();
            return $rajaongkir->result->getCity($this->row->nonsibling_id_geodirectory);
        }
        return false;
    }
}