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
 * Class Media
 * @package App\Api\Modules\Posts\Models
 */
class Media extends Model
{
    /**
     * Media::$table
     *
     * @var string
     */
    public $table = 'posts_media';

    /**
     * Media::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post',
        'id_media',
        'default',
    ];

    /**
     * Media::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'post',
        //'media',
        'image'
    ];

    // ------------------------------------------------------------------------

    /**
     * Media::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }

    // ------------------------------------------------------------------------

    /**
     * Media::media
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function media()
    {
        return $this->belongsTo(\App\Api\Modules\Media\Models\Media::class, 'id_media');
    }

    public function image()
    {
        $data = $this->belongsTo(\App\Api\Modules\Media\Models\Media::class, 'id_media');
        if ($data) {
            if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $result->filename)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg'); 
        } 
        return storage_url('/images/default/no-image.jpg');
    }

    public function images() {
        $data = $this->belongsTo(\App\Api\Modules\Media\Models\Media::class, 'id_media');
        if ($data) {
            return $data;
        }
        return false;
    }

    // public function images()
    // {
    //     $this->qb->where('name', 'photo');
    //     if($result = $this->hasOne(Metadata::class, 'id_place')) {
    //         if (is_file($filePath = PATH_STORAGE . 'images/places/' . $result->content)) {
    //             return storage_url($filePath);
    //         }
    //         return storage_url('/images/default/no-image.jpg');
    //     }

    //     return storage_url('/images/default/no-image.jpg');
    // }
}

