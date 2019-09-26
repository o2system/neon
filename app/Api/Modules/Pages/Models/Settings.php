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

namespace App\Api\Modules\Pages\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Settings
 * @package App\Api\Modules\Pages\Models
 */
class Settings extends Model
{
    use RelationTrait;

    /**
     * Settings::$table
     *
     * @var string
     */
    public $table = 'pages_settings';

    /**
     * Settings::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_page',
        'key',
        'value',
    ];

    /**
     * Settings::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'page'
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::page
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function page()
    {
        return $this->belongsTo(Pages::class, 'id_page');
    }
}
