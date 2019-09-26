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

namespace App\Api\Modules\Pages\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Posts
 * @package App\Api\Modules\Pages\Models
 */
class Posts extends Model
{
    /**
     * Posts::$table
     *
     * @var string
     */
    public $table = 'pages_posts';

    // ------------------------------------------------------------------------

    /**
     * Posts::page
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function page()
    {
        return $this->belongsTo(Pages::class, 'id_page');
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}