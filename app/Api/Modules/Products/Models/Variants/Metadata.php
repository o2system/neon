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

namespace App\Api\Modules\Products\Models\Variants;

// ------------------------------------------------------------------------

use App\Api\Modules\Products\Models\Products;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Metadata
 * @package App\Api\Modules\Products\Models\Tags
 */
class Metadata extends Model
{
    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'products_variants_metadata';

    public $appendColumns = [
        //'post'
        'record'
    ];
}