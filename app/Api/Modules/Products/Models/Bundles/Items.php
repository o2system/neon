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

namespace App\Api\Modules\Products\Models\Bundles;

// ------------------------------------------------------------------------

use App\Api\Modules\Products\Models\Products;
use App\Api\Modules\Products\Models\Tags;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Items
 * @package App\Api\Modules\Products\Models\Tags
 */
class Items extends Model
{
    /**
     * Items::$table
     *
     * @var string
     */
    public $table = 'products_bundles_items';

    /**
     * Items::$visibleColumns
     *
     * @var array
     */

    public $appendColumns = [
        //'post'
        'record'
    ];
}