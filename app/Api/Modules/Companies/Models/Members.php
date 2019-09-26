<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Companies\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Members\Models\Members as MembersModule;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Users
 * @package App\Api\Modules\Companies\Models
 */
class Members extends Model
{
    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'companies_members';

    /**
     * Users::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_company',
        'id_member',
    ];

    /**
     * Users::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'company',
        'user'
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::company
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function company()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::place
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function member()
    {
        return $this->belongsTo(MembersModule::class, 'id_member');
    }

    public function total_followers()
    {
        if (globals()->account->company) {
            if (count($data = $this->findWhere(['id_company' => globals()->account->company->id]))) {
                return count($data);
            }
            return 0;
        }
        return 0;
    }
}
