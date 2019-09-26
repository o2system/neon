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

use O2System\Framework\Models\Sql\Model;

/**
 * Class Metadata
 * @package App\Api\Modules\Posts\Models
 */
class Metadata extends Model
{
    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'posts_metadata';

    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'post'
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}