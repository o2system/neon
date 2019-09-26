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

namespace App\Api\Modules\Posts\Models\Sections;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Models\Posts;
use App\Api\Modules\Posts\Models\Sections;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Items
 * @package App\Api\Modules\Posts\Models\Sections
 */
class Items extends Model
{
    /**
     * Items::$table
     *
     * @var string
     */
    public $table = 'posts_sections_items';

    /**
     * Items::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post_section',
        'id_post',
    ];

    /**
     * Items::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'section',
        //'post',
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Items::section
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function section()
    {
        return $this->belongsTo(Sections::class, 'id_post_section');
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