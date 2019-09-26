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

namespace App\Api\Modules\Posts\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Models\Tags\Items;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Tags
 * @package App\Api\Modules\Posts\Models
 */
class Tags extends Model
{
    /**
     * Tags::$table
     *
     * @var string
     */
    public $table = 'posts_tags';

    /**
     * Tags::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'name',
    ];

    public $appendColumns = [
        //'post'
        // 'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::items
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function items()
    {
        return $this->hasMany(Items::class, 'id_post_tag');
    }
}