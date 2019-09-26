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

namespace App\Api\Modules\Posts\Models\Tags;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Models\Posts;
use App\Api\Modules\Posts\Models\Tags;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Items
 * @package App\Api\Modules\Posts\Models\Tags
 */
class Items extends Model
{
    /**
     * Items::$table
     *
     * @var string
     */
    public $table = 'posts_tags_items';

    /**
     * Items::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post_tag',
        'id_post'
    ];

    public $appendColumns = [
        //'post'
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Items::tag
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function tag()
    {
        return $this->belongsTo(Tags::class, 'id_post_tag');
    }

    // ------------------------------------------------------------------------

    /**
     * Items::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}