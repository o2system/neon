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
 * Class Banks
 * @package App\Api\Modules\Master\Models
 */
class Banks extends Model
{
    /**
     * Banks::$table
     *
     * @var string
     */
    public $table = 'tm_banks';

    public $appendColumns = [
    	'image'
    ];

    public function image()
    {
    	if ($metadata = $this->row->metadata) {
    		if (is_file($image = PATH_STORAGE . 'images/master/banks/' . $metadata['photo'])) {
    			return storage_url($image);
    		}
    		return storage_url('images/default/no-image.jpg');
    	} else {
    		return storage_url('images/default/no-image.jpg');
    	}
    }

    // ------------------------------------------------------------------------
    public function insert($post)
    {
    	if ($post) {
    		if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/master/banks/';
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($filePath);
                $upload->process('photo');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    $this->output->send([
                        'error'  => $errors
                    ]);
                } else {
                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['metadata']['photo'] = $filename;
                }
            }
            if (parent::insert($post)) {
            	return true;
            }
            return false;
    	} else {
    		return false;
    	}
    }

    public function update($post, $conditions = null)
    {
    	if ($post) {
    		if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/master/banks/';
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($filePath);
                $upload->process('photo');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    $this->output->send([
                        'error'  => $errors
                    ]);
                } else {
                	if ($post->id) {
                        $data = $this->find($post->id);
                        if (is_file($image = $filePath.$data->metadata['photo'])) {
                            unlink($image);
                        }
                    }
                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['metadata']['photo'] = $filename;
                }
            }
            if (parent::update($post)) {
            	return true;
            }
            return false;
    	} else {
    		return false;
    	}
    }

    public function filter($get)
    {
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

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
