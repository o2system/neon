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

namespace App\Api\Modules\Master\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Currencies
 * @package App\Api\Modules\Master\Models
 */
class Currencies extends Model
{
    /**
     * Currencies::$table
     *
     * @var string
     */
    public $table = 'tm_currencies';

    // ------------------------------------------------------------------------
    public function insert($post)
    {
    	if ($post) {
            if (parent::insert($post)) {
            	return redirect_url(domain_url('/master/currencies', null, 'manage'));
            }
            return false;
    	} else {
    		return false;
    	}
    }

    public function update($post, $conditions = null)
    {
    	if ($post) {
            if (parent::update($post)) {
            	return redirect_url(domain_url('/master/currencies', null, 'manage'));
            }
            return false;
    	} else {
    		return false;
    	}
    }

    public function delete($id)
    {
    	if ($id) {
    		if (parent::delete($id)) {
    			redirect_url($_SERVER["HTTP_REFERER"]);
    		}
    	}
    }

    public function filter($get)
    {
        if ($keyword = $get->keyword) {
            $this->qb->like('name', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging();
        }

        return $all;
    }
}
