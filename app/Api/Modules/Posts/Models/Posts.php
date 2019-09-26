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
use O2System\Spl\DataStructures\SplArrayObject;
use App\Api\Modules\Media\Models\Media as RealMedia;

/**
 * Class Posts
 * @package App\Api\Modules\Posts\Models
 */
class Posts extends Model
{
    /**
     * Posts::$table
     *
     * @var string
     */
    public $table = 'posts';

    /**
     * Posts::$visibleColumns
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
     * Posts::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'image',
        // 'metadata',
        // 'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Posts::visibilityOptions
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
     * Posts::media
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function medias()
    {
        $datas = $this->hasMany(Media::class, 'id_post');
        if (count($datas)) {
            $medias = [];
            $index = 0;
            foreach ($datas as $data) {
                if ($image = $data->images) {
                    if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
                        $medias[$index++] = [
                            'id' => $image->id,
                            'name' => $image->filename,
                            'image' => storage_url($filePath)
                        ];
                    } else {
                        $medias[$index++] = [
                            'id' => $image->id,
                            'name' => $image->filename,
                            'image' => storage_url('/images/default/no-image.jpg')
                        ];
                    }
                }
            }
            if (count($medias)) {
                return $medias;
            }
            return false;
         } 
        return false;
    }

    public function total_media()
    {
        $datas = $this->hasMany(Media::class, 'id_post');
        if (count($datas)) {
            $total = 0;
            foreach ($datas as $data) {
                if ($data->media) {
                    $total++;
                }
            }
            return $total;
        }
        return 0;
    }

    public function image()
    {
        models(Media::class)->appendColumns = ['images'];
        $data = $this->hasMany(Media::class, 'id_post');
        if (count($data)) {
            if ($image = $data->first()->images) {
                if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
                    return storage_url($filePath);
                }
                return storage_url('/images/default/no-image.jpg');
            }
             return storage_url('/images/default/no-image.jpg');
         }
        return storage_url('/images/default/no-image.jpg');
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata()
    {
        if($result = $this->hasMany(Metadata::class, 'id_post')) {
            $metadata = new SplArrayObject();
            foreach($result as $row) {
                $metadata->offsetSet($row->name, $row->content);
            }

            return $metadata;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::settings
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function settings()
    {
        if($result = $this->hasMany(Settings::class, 'id_post')) {
            $setting = new SplArrayObject();
            foreach($result as $row) {
                $setting->offsetSet($row->key, $row->value);
            }

            return $setting;
        }

        return null;
    }

    /**
     * Posts::settings
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function tag()
    {
        $data = $this->hasOne(Tags\Items::class, 'id_post');
        if ($data) {
            if ($data->tag) {
                return $data->tag;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::recurrence
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function recurrence()
    {
        return $this->hasOne(Recurrences::class, 'id_post');
    }

    // ------------------------------------------------------------------------
    
    public function insert($post)
    {
        if ($post) {
            $metas = $post['meta'];
            $settings = $post['settings'];
            $tags = $post['tags'];

            unset($post['meta'], $post['settings'], $post['tags']);
            if (parent::insert($post)) {
                $id_post = $this->getLastInsertId();

                if (count($metas)) {
                    $metadata = [];
                    $no=0;
                    foreach ($metas as $key => $value) {
                        models(Metadata::class)->insertOrUpdate([
                            'id_post' => $id_post,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ], [
                            'id_post' => $id_post,
                            'name' => $key,
                        ]);
                    }
                }               

                if (count($settings)) {
                    $settings_meta = [];
                    $no=0;
                    foreach ($settings as $key => $value) {
                        models(Settings::class)->insertOrUpdate([
                            'id_post' => $id_post,
                            'key' => $key,
                            'value' => $value,
                            'record_create_timestamp' => timestamp()
                        ], [
                            'id_post' => $id_post,
                            'key' => $key,
                        ]);
                    }
                    
                }
                
                if (count($tags)) {
                    models(Tags::class)->insert($tags);
                    $id_tag = $this->getLastInsertId();
                    if ($id_post) {
                        $tags_items = [
                            'id_post_tag' => $id_tag,
                            'id_post' => $id_post
                        ];
                        models(Tags\Items::class)->insert($tags_items);
                    }
                }

                if (models(Media::class)->findWhere(['id_post' => 0])) {
                    models(Media::class)->update(['id_post' => $id_post], ['id_post' => 0]);
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
            $tags = $post['tags'];
            unset($post['meta'], $post['settings'], $post['tags'], $post['status']);
            if (parent::update($post)) {
                $id_post = $post['id'];

                if (count($metas)) {
                    $metadata = [];
                    $no=0;
                    foreach ($metas as $key => $value) {
                        models(Metadata::class)->insertOrUpdate([
                            'id_post' => $id_post,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ], [
                            'id_post' => $id_post,
                            'name' => $key,
                        ]);
                    }
                }               

                if (count($settings)) {
                    $settings_meta = [];
                    $no=0;
                    foreach ($settings as $key => $value) {
                        models(Settings::class)->insertOrUpdate([
                            'id_post' => $id_post,
                            'key' => $key,
                            'value' => $value,
                            'record_create_timestamp' => timestamp()
                        ], [
                            'id_post' => $id_post,
                            'key' => $key,
                        ]);
                    }
                }

                if (count($tags)) {
                    $id_tag = models(Tags\Items::class)->find($post['id'], 'id_post', 1)->id_post_tag;
                    models(Tags::class)->update($tags, ['id' => $id_tag]);
                    if ($id_slider) {
                        $tags_items = [
                            'id_post_tag' => $id_tag,
                            'id_post' => $id_post
                        ];
                        models(Tags\Items::class)->insertOrUpdate($tags_items, ['id_post' => $id_post]);
                    }
                }

                if (models(Media::class)->findWhere(['id_post' => 0])) {
                    models(Media::class)->update(['id_post' => $id_post], ['id_post' => 0]);
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
            // models(Metadata::class)->deleteManyBy(['id_post' => $id]);
            // models(Settings::class)->deleteManyBy(['id_post' => $id]);
            // $tag = models(Tags\Items::class)->find($id, 'id_post')->id_post_tag;
            // if ($tag) {
            //     models(Tags::class)->deleteManyBy(['id' => $tag]);
            // }
            // models(Tags\Items::class)->deleteManyBy(['id_post' => $id]);
            // models(Media::class)->appendColumns = ['images'];
            // if ($medias = models(Media::class)->findWhere(['id_post' => $id])) {
            //     foreach ($medias as $media) {
            //         if ($image = $media->images) {
            //             if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
            //                 unlink($filePath);
            //             }
            //             models(RealMedia::class)->delete($image->id);
            //         }
            //     }
            //     models(Media::class)->deleteManyBy(['id_post' => $id]);
            // }
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

    public function sliders_filter($get, $id_taxonomy)
    {
        $this->qb->where('id_taxonomy', $id_taxonomy);
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(start_publishing) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(finish_publishing) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('title', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging();
        }

        return $all;
    }
}