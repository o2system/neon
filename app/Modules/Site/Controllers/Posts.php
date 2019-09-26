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

namespace App\Manage\Modules\Site\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Site\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Media\Models\Media;
use App\Api\Modules\Posts\Models\Media as PostsMedia;

/**
 * Class Posts
 * @package App\Manage\Modules\Posts\Controllers
 */
class Posts extends Controller
{
    public $model = '\App\Api\Modules\Posts\Models\Posts';
	public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        $this->model->qb->where('record_status', 'PUBLISH');
        $all = $this->model->allWithPaging();
        $vars = [
            'posts' => $all,
            'get' => $get
        ];
        view('posts/index', $vars);
    }

    public function drafts()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        $this->model->qb->where('record_status', 'DRAFT');
        $all = $this->model->allWithPaging();
        $vars = [
            'posts' => $all,
            'get' => $get
        ];
        view('posts/drafts', $vars);
    }

    public function trash()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        $this->model->qb->where('record_status', 'DELETED');
        $all = $this->model->allWithPaging();
        $vars = [
            'posts' => $all,
            'get' => $get
        ];
        view('posts/trash', $vars);
    }

    public function form($id=null)
    {
        models(PostsMedia::class)->appendColumns = ['images'];
        if ($medias = models(PostsMedia::class)->findWhere(['id_post' => 0])) {
            foreach ($medias as $media) {
                if ($image = $media->images) {
                    if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
                        unlink($filePath);
                    }
                    models(Media::class)->delete($image->id);
                }
            }
            models(PostsMedia::class)->deleteManyBy(['id_post' => 0]);
        }
        $this->presenter->page->setHeader( 'FORM_ADD' );
        $vars = [
            'post' => new SplArrayObject(),
            'visibility' => visibilityOptions(),
            'status' => posts_status(),
            'world_languages' => world_languages()
        ];

        $this->model->appendColumns = [
            'metadata', 'settings', 'tag', 'record'
        ];

        if ($id) {
            $this->model->qb->where('record_status !=', 'DELETED');
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                $this->model->appendColumns = [
                    'metadata', 'settings', 'tag', 'record', 'medias'
                ];
                $vars['post'] = $data;
                // print_out($vars);
            } else {
                $this->output->send(204);
            }
        }
        // print_out($data);

        view('posts/form', $vars);
    }

    public function deleteImage($id=null)
    {
        if ($id) {
            models(PostsMedia::class)->appendColumns = ['images'];
            if ($medias = models(PostsMedia::class)->findWhere(['id_media' => $id])) {
                foreach ($medias as $media) {
                    if ($image = $media->images) {
                        if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
                            unlink($filePath);
                        }
                        models(Media::class)->delete($image->id);
                    }
                }
                models(PostsMedia::class)->deleteBy(['id_media' => $id]);
            }
            return redirect_url(input()->server('HTTP_REFERER'));
        } else {
            $this->output->send(404);
        }
    }
}