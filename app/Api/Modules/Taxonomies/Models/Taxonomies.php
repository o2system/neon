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
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
use App\Api\Modules\Posts\Models\Posts;

/**
 * Class Taxonomies
 * @package App\Api\Modules\Taxonomies\Models
 */
class Taxonomies extends Model
{
    use HierarchicalTrait;
    /**
     * Taxonomies::$table
     *
     * @var string
     */
    public $table = 'taxonomies';

    /**
     * Taxonomies::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_taxonomy_term',
        'id_parent',
        'name',
        'slug',
    ];

    public $appendColumns = [
        'image',
        'record',
        'check_child'
    ];

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::metadata
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function term()
    {
        return $this->belongsTo(Terms::class, 'id_taxonomy_term');
    }

    public function check_child()
    {
        $data = $this->findWhere(['id_parent' => $this->row->id]);
        if (count($data)) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata($input = null)
    {
        if ($input) {
            $this->qb->where('name', $input);
        }
        if($result = $this->hasMany(Metadata::class, 'id_taxonomy')) {
            $metadata = new SplArrayObject();
            foreach($result as $row) {
                $metadata->offsetSet($row->name, $row->content);
            }

            return $metadata;
        }

        return null;
    }

    public function image()
    {
        $this->qb->where('name', 'photo');
        if($result = $this->hasOne(Metadata::class, 'id_taxonomy')) {
            if (is_file($filePath = PATH_STORAGE . 'images/master/taxonomies/' . $result->content)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }

        return storage_url('/images/default/no-image.jpg');
    }

    public function total_sliders()
    {
        $data = $this->hasMany(Posts::class, 'id_taxonomy');
        if (count($data)) {
            return count($data);
        }
        return 0;
    }

    public function insert($post)
    {
        if ($post) {
            $post['slug'] = str_replace(' ', '-', strtolower($post['name']));

            if($files = input()->files()['photo']){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/master/taxonomies/';
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
                    $metadata = [
                        'name' => 'photo',
                        'content' => $filename,
                        'record_create_timestamp' => timestamp()
                    ];
                    
                }
            }
            
            if (parent::insert($post)) {
                $this->orderBy('id', 'DESC');
                $idTaxonomy = $this->all(null, 1)[0]->id;
                if (count($metadata)) {
                    $metadata['id_taxonomy'] = $idTaxonomy;
                    models(Metadata::class)->insert($metadata);
                }
                
                return ['status' => true, 'id_taxonomy' => $idTaxonomy];
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
                $filePath = PATH_STORAGE . 'images/master/taxonomies/';
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
                        if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
                            unlink($image);
                        }
                    }
                    
                    $metadata = [
                        'name' => 'photo',
                        'content' => $filename,
                        'record_create_timestamp' => timestamp()
                    ];
                    
                }
            }
            if (parent::update($post)) {
                $idTaxonomy = $post['id'];
                if (count($metadata)) {
                    $metadata['id_taxonomy'] = $idTaxonomy;
                    models(Metadata::class)->deleteBy([
                        'id_taxonomy' => $post['id'],
                        'name' => 'photo'
                    ]);
                    models(Metadata::class)->insert($metadata);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    public function sliders_filter($get, $term_id)
    {
        $this->qb->where('id_taxonomy_term', $term_id);
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