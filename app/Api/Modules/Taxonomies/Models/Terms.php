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

namespace App\Api\Modules\Taxonomies\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Terms
 * @package App\Api\Modules\Taxonomies\Models
 */
class Terms extends Model
{
    /**
     * Terms::$table
     *
     * @var string
     */
    public $table = 'taxonomies_terms';

    /**
     * Terms::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'name',
        'slug',
        'description',
        'image',
        'metadata'
    ];

    public $appendColumns = [
        'check_taxonomies',
        'record',
        'photo'
    ];

    // ------------------------------------------------------------------------

    /**
     * Terms::taxonomies
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function taxonomies()
    {
        return $this->hasMany(Taxonomies::class, 'id_taxonomy_term');
    }

    public function check_taxonomies()
    {
        $data = $this->hasMany(Taxonomies::class, 'id_taxonomy_term');
        if (count($data)) {
             return true;
         } 
        return false;
    }

    public function photo()
    {
        $filePath = PATH_STORAGE . 'images/master/taxonomies/terms/'.$this->row->image;
        if (is_file($filePath)) {
            return storage_url($filePath);
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function insert($post)
    {
        if ($post) {
            if($files = input()->files()['photo']){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/master/taxonomies/terms/';
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
                    $post['image'] = $filename;
                }
            }
            $post['slug'] = str_replace(' ', '-', strtolower($post['name']));
            if (parent::insert($post)) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function update($post, $conditions = null)
    {
        if ($post) {
            if($files = input()->files()['photo']){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/master/taxonomies/terms/';
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
                    $post['image'] = $filename;
                }
            }
            if (parent::update($post)) {
                return true;
            }
            return false;
        }
        return false;
    }
}