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

namespace App\Api\Modules\Pages\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Pages
 * @package App\Api\Modules\Pages\Models
 */
class Pages extends Model
{
    use RelationTrait;

    /**
     * Pages::$table
     *
     * @var string
     */
    public $table = 'pages';

    /**
     * Pages::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'title',
        'slug',
        'excerpt',
        'content',
        'visibility',
        'start_publishing',
        'finish_publishing'
    ];

    /**
     * Pages::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'media',
        'metadata'
    ];

    // ------------------------------------------------------------------------

    /**
     * Pages::visibilityOptions
     *
     * @return array
     */
    public function visibilityOptions()
    {
        return [
            'PUBLIC' => language('PUBLIC'),
            'READONLY' => language('READONLY'),
            'PROTECTED' => language('PROTECTED'),
            'PRIVATE' => language('PRIVATE')
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata($input=null)
    {
        if ($input) {
            $this->qb->where('name', $input);
        }
        if($result = $this->hasMany(Metadata::class, 'id_page')) {
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
        if($result = $this->hasOne(Metadata::class, 'id_page')) {
            if (is_file($filePath = PATH_STORAGE . 'images/pages/media/' . $result->content)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }

        return storage_url('/images/default/no-image.jpg');
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::posts
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function posts()
    {
        return $this->hasMany(Posts::class, 'id_page');
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::settings
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function settings()
    {
        if($result = $this->hasMany(Settings::class, 'id_page')) {
            $setting = new SplArrayObject();
            foreach($result as $row) {
                $setting->offsetSet($row->key, $row->value);
            }

            return $setting;
        }

        return null;
    }

    public function insert($post)
    {
        if ($post) {
            $metas = $post['meta'];
            $settings = $post['settings'];
            unset($post['meta'], $post['settings']);
            if (parent::insert($post)) {
                $id_page = $this->getLastInsertId();
                if($files = ($_FILES['photo']['name'] != null ? $_FILES : false)){
                    $metadata = [];
                    $filePath = PATH_STORAGE . 'images/pages/media/';
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
                        $metadata_image = [
                            'id_page' => $id_page,
                            'name' => 'photo',
                            'content' => $filename,
                            'record_create_timestamp' => timestamp()
                        ];
                        models(Metadata::class)->insert($metadata_image);
                    }
                }

                if (count($metas)) {
                    $metadata = [];
                    $no=0;
                    foreach ($metas as $key => $value) {
                        $metadata[$no++] = [
                            'id_page' => $id_page,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ];
                        
                    }
                    models(Metadata::class)->insertMany($metadata);
                }               

                if (count($settings)) {
                    $settings_meta = [];
                    $no=0;
                    foreach ($settings as $key => $value) {
                        $settings_meta[$no++] = [
                            'id_page' => $id_page,
                            'key' => $key,
                            'value' => $value,
                            'record_create_timestamp' => timestamp()
                        ];
                    }
                    models(Settings::class)->insertMany($settings_meta);
                }

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
            if ($post['restore_status']) {
                unset($post['restore_status']);
                if (parent::update($post)) {
                    return true;
                }
            }
            $metas = $post['meta'];
            $post['slug'] = str_replace(' ', '-', strtolower($post['slug']));
            $settings = $post['settings'];
            unset($post['meta'], $post['settings']);
            if (parent::update($post)) {
                $id_page = $post['id'];
                if($files = ($_FILES['photo']['name'] != null ? $_FILES : false)){
                    $metadata = [];
                    $filePath = PATH_STORAGE . 'images/pages/media/';
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
                            models(Metadata::class)->deleteBy(['id_page' => $id_page, 'name' => 'photo']);
                        }
                        $metadata_image = [
                            'id_page' => $id_page,
                            'name' => 'photo',
                            'content' => $filename,
                            'record_create_timestamp' => timestamp()
                        ];
                        models(Metadata::class)->insert($metadata_image);
                    }
                }
                
                if (count($metas)) {
                    $metadata = [];
                    $no=0;
                    foreach ($metas as $key => $value) {
                        $metadata[$no++] = [
                            'id_page' => $id_page,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ];
                        models(Metadata::class)->deleteBy(['id_page' => $id_page, 'name' => $key]);
                    }
                    models(Metadata::class)->insertMany($metadata);
                }               

                if (count($settings)) {
                    $settings_meta = [];
                    $no=0;
                    foreach ($settings as $key => $value) {
                        $settings_meta[$no++] = [
                            'id_page' => $id_page,
                            'key' => $key,
                            'value' => $value,
                            'record_create_timestamp' => timestamp()
                        ];
                    }
                    models(Settings::class)->deleteManyBy(['id_page' => $id_page]);
                    models(Settings::class)->insertMany($settings_meta);
                }

                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        if ($id) {
            // $meta_image = models(Metadata::class)->findWhere(['id_page' => $id, 'name' => 'photo']);
            // if ($meta_image->count()) {
            //     $meta_image = $meta_image->first();
            //     if (is_file($filePath = PATH_STORAGE . 'images/pages/media/' . $meta_image->content)) {
            //         unlink($filePath);
            //     }
            // }
            // models(Metadata::class)->deleteManyBy(['id_page' => $id]);
            // models(Settings::class)->deleteManyBy(['id_page' => $id]);
            if (is_array($id)) {
                if ($id['status'] == 'delete') {
                    if (parent::delete($id['id'])) {
                        return true;
                    }
                }
            }
            if (parent::update(['record_status' => 'DELETED', 'id' => $id])) {
                return true;
            }
        }
    }
}
