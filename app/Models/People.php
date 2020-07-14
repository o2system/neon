<?php

/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace App\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Relationships;
use O2System\Framework\Models\Sql\System\Users;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;

/**
 * Class People
 * @package App\Models
 */
class People extends Model
{
    use MetadataTrait;

    /**
     * People::$table
     *
     * @var string
     */
    public $table = 'people';

    /**
     * People::$uploadFilePaths
     *
     * @var array
     */
    // public $uploadFilePaths = [
    //     'avatar' => PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR . 'people' . DIRECTORY_SEPARATOR,
    //     'cover' => PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR . 'people' . DIRECTORY_SEPARATOR
    // ];

    /**
     * People::$appendColumns
     *
     * @var string
     */
    public $appendColumns = [
        'avatar_url'
    ];

    // ------------------------------------------------------------------------

    /**
     * People::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function user()
    {
        return $this->morphByOneThrough(Users::class, Relationships::class, 'relation');
    }

    // ------------------------------------------------------------------------

    /**
     * People::avatar_url
     *
     * @return string
     */
    // public function avatar_url()
    // {
    //     if (is_file($filePath = $this->uploadedImageFilePath . $this->row->avatar)) {
    //         return images_url($filePath);
    //     }

    //     if (is_file($avatarFilePath = PATH_STORAGE . 'images/default/avatar-' . strtolower($this->row->gender) . '.png')) {
    //         return images_url($avatarFilePath);
    //     } elseif (is_file($avatarFilePath = PATH_STORAGE . 'images/default/avatar.png')) {
    //         return images_url($avatarFilePath);
    //     }
    // }
}
