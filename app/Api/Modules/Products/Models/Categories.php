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

namespace App\Api\Modules\Products\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Security\Generators\Uid;
use App\Api\Modules\Companies\Models\Companies;

/**
 * Class Categories
 * @package App\Api\Modules\Products\Models
 */
class Categories extends Model
{
    /**
     * Categories::$table
     *
     * @var string
     */
    public $table = 'products_categories';

    public $visibleColumns = [
    	'id',
    	'id_parent',
    	'id_google_product_taxonomy',
    	'name',
    	'slug',
    	'code',
    	'description',
    	'photo',
        'record_dept'
    ];

    public $appendColumns = [
    	'check_child',
    	'image'
    ];

    public function merchants_products_categories_all()
    {
        if ($this->row->id) {
            models(Companies::class)->qb->select('companies.*');
            models(Companies::class)->qb->join('companies_products', 'companies_products.id_company = companies.id');
            models(Companies::class)->qb->join('products', 'products.id = companies_products.id_product');
            models(Companies::class)->qb->join('products_categories', 'products.id_product_category = products_categories.id');
            models(Companies::class)->qb->where('products_categories.id', $this->row->id);
            models(Companies::class)->qb->groupBy('companies.id');
            return models(Companies::class)->all();
        } else {
            return false;
        }
    }

    public function creditors_products_categories_all()
    {
        if ($this->row->id) {
            models(Companies::class)->qb->select('companies.*');
            models(Companies::class)->qb->join('interests', 'interests.id_company = companies.id');
            models(Companies::class)->qb->join('products_categories', 'interests.id_product_category = products_categories.id');
            models(Companies::class)->qb->where('products_categories.id', $this->row->id);
            models(Companies::class)->qb->groupBy('companies.id');
            return models(Companies::class)->all();
        } else {
            return false;
        }
    }

    public function check_child()
    {
        $data = $this->findWhere(['id_parent' => $this->row->id]);
        if (count($data)) {
            return true;
        }
        return false;
    }

    public function image()
    {
        $filePath = PATH_STORAGE . 'images/products/categories/'.$this->row->photo;
        if (is_file($filePath)) {
            return storage_url($filePath);
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function insert($post)
    {
    	if ($post) {
    		$post['slug'] = str_replace(' ', '-', strtolower($post['name']));
    		$post['code'] = Uid::generate(5);
    		if($files = input()->files()['photo']){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/products/categories/';
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
                    if ($post['id']) {
                        $data = $this->find($post['id']);
                        if (is_file($image = $filePath.$data->image)) {
                            unlink($image);
                        }
                    }
                    $post['photo'] = $filename;
                }
            }
    		if (parent::insert($post)) {
    			return true;
    		}
    		return false;
    	}
    }

    public function update($post, $conditions = [])
    {
    	if ($post) {
    		$post['code'] = Uid::generate(5);
    		if($files = input()->files()['photo']){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/products/categories/';
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
                    if ($post['id']) {
                    	$this->appendColumns = [];
                        $data = $this->find($post['id']);
                        if (is_file($image = $filePath.$data->photo)) {
                            unlink($image);
                        }
                    }
                    $post['photo'] = $filename;
                }
            }
	    	if (parent::update($post)) {
    			return true;
    		}
    		return false;
    	}
    }

    public function delete($id)
	{
		if ($id) {
			$this->appendColumns = [];
			$data = $this->find($id);
			$filePath = PATH_STORAGE . 'images/products/categories/';
            if (is_file($image = $filePath.$data->photo)) {
                unlink($image);
            }
			if (parent::delete($id)) {
				return true;
			}
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

