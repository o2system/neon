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

namespace App\Api\Modules\Posts\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use App\Api\Modules\Media\Models\Media;
use App\Api\Modules\Posts\Models\Media as PostsMedia;
use App\Api\Modules\Posts\Models\Posts as RealPosts;
use App\Api\Modules\Posts\Models\Metadata;
use App\Api\Modules\Posts\Models\Settings;
use App\Api\Modules\Posts\Models\Tags;
use App\Api\Modules\Posts\Models\Tags\Items;

/**
 * Class Posts
 * @package App\Api\Modules\Posts\Controllers
 */
class Posts extends Controller
{
    /**
     * Posts::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'id',
    //         'label'    => 'Id',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'title',
    //         'label'    => 'Title',
    //         'rules'    => 'required|alphanumericspaces',
    //         'messages' => 'Title cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
    //     ],
    //     [
    //         'field'    => 'slug',
    //         'label'    => 'Slug',
    //         'rules'    => 'required|alphadash',
    //         'messages' => 'Slug cannot be empty! the examples are like this = slug-slug-slug',
    //     ],
    //     [
    //         'field'    => 'excerpt',
    //         'label'    => 'Excerpt',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'content',
    //         'label'    => 'Content',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'visibility',
    //         'label'    => 'Visibility',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'start_publishing',
    //         'label'    => 'Start Publishing',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'finish_publishing',
    //         'label'    => 'Finish Publishing',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'meta',
    //         'label'    => 'meta',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'settings',
    //         'label'    => 'meta',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'tags',
    //         'label'    => 'meta',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'record_status',
    //         'label'    => 'Status',
    //         'rules'    => 'optional',
    //     ],
    // ];

    public function create()
    {
        $_POST['id'] = null;
        $_POST['slug'] = str_replace(' ', '-', strtolower($_POST['slug']));
        if ($_POST['slug'] == null) {
            $_POST['slug'] = str_replace(' ', '-', strtolower($_POST['title']));
        }
        
        parent::create();
    }

    public function upload()
    {
        if ($files = input()->files('file')) {
            $filePath = PATH_STORAGE . 'images/posts/media/';
            if(!file_exists($filePath)){
                mkdir($filePath, 0777, true);
            }
            $upload = new Uploader();
            $upload->setPath($filePath);
            $upload->process('file');

            if (count($upload->getErrors())) {
                $errors = new Unordered();

                foreach ($upload->getErrors() as $code => $error) {
                    $errors->createList($error);
                }
                $this->sendError(404, $errors);
            } else {
                $file = $upload->getUploadedFiles()->first();
                $mime_data = explode('/', $file['mime']);

                if ($mime_data[0] != 'image') {
                    $mime = 'OTHER';
                    $label = strtoupper($mime_data[1]);
                } elseif ($mime_data[0] == 'image') {
                    $mime = 'IMAGE';
                    $label = strtoupper($mime_data[1]);
                }

                $data = [
                    'label' => 'SITE_POSTS_MEDIA',
                    'filename' => $file['name'],
                    'mime' => $mime,
                    'record_create_timestamp' => timestamp()
                ];
                
                if (!models(Media::class)->insert($data)) {
                    return $this->sendError(404, 'MEDIA_ERROR');
                }
                $id_media = models(Media::class)->getLastInsertId();
                $posts_media = [
                    'id_media' => $id_media,
                    'id_post' => 0,
                    'default' => 'NO',
                    'record_create_timestamp' => timestamp()
                ];
                if (!models(PostsMedia::class)->insert($posts_media)) {
                    return $this->sendError(404, 'ERROR_POSTSMEDIA');
                }
                $this->sendError(201, 'It\'s a Success! Good Job!');
            }
        } else {
            return $this->sendError(404, 'NO_FILE');
        }
    }

    public function trash()
    {
        if ($post = input()->post()) {
            if ($post->status == 'delete') {
                $id = $post->id;
                models(Metadata::class)->deleteManyBy(['id_post' => $id]);
                models(Settings::class)->deleteManyBy(['id_post' => $id]);
                $tag = models(Items::class)->find($id, 'id_post')->id_post_tag;
                if ($tag) {
                    models(Tags::class)->deleteManyBy(['id' => $tag]);
                }
                models(Items::class)->deleteManyBy(['id_post' => $id]);
                models(PostsMedia::class)->appendColumns = ['images'];
                if ($medias = models(PostsMedia::class)->findWhere(['id_post' => $id])) {
                    foreach ($medias as $media) {
                        if ($image = $media->images) {
                            if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
                                unlink($filePath);
                            }
                            models(Media::class)->delete($image->id);
                        }
                    }
                    models(PostsMedia::class)->deleteManyBy(['id_post' => $id]);
                }
                if (models(RealPosts::class)->delete($post->getArrayCopy())) {
                    $this->sendError(201, 'Successful delete request');
                } else {
                    $this->sendError(501, 'Failed delete request');
                }
            }            
        } else {
            $this->sendError(400);
        }
    }

    public function restore()
    {
        if ($post = input()->post()) {
            if ($post->status == 'restore') {
                $id = $post->id;
                $data = [];
                $data['record_status'] = 'DRAFT';
                $data['id'] = $id;
                $data['restore_status'] = 1;
                if (models(RealPosts::class)->update($data)) {
                    $this->sendError(201, 'Successful restore request');
                } else {
                    $this->sendError(501, 'Failed restore request');
                }
            }
        } else {
            $this->sendError(400);
        }
    }

}
