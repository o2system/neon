<?php
/**
 * This file is part of the WebIn Platform.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Models\Master;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Banks
 * @package App\Models\Master
 */
class Banks extends Model
{
    /**
     * Banks::$table
     *
     * @var string
     */
    public $table = 'tm_banks';

    // ------------------------------------------------------------------------

    /**
     * Banks::logo
     *
     * @return string
     */
    public function logo(): string
    {
        if (is_file($filePath = PATH_STORAGE . 'images/bank/' . dash(strtolower($this->row->alias)))) {
            return images_url($filePath);
        }
        return images_url('default/logo.png');
    }
}
