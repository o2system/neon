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

namespace App\Manage\Modules\Site\Controllers\Appearance\Sliders;

// ------------------------------------------------------------------------

use App\Manage\Modules\Site\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Taxonomies\Models\Taxonomies;
use App\Api\Modules\Taxonomies\Models\Terms;
use App\Api\Modules\Media\Models\Media;
use App\Api\Modules\Posts\Models\Media as PostsMedia;

/**
 * Class Detail
 * @package App\Manage\Modules\Sites\Controllers
 */
class Detail extends Controller
{
	public $model = '\App\Api\Modules\Posts\Models\Posts';
	public function index($id)
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
    	$vars = [
            'taxonomy' => models(Taxonomies::class)->find($id),
    		'sliders' => $this->model->sliders_filter($get, $id),
            'entries' => range(10, 100, 10),
            'get' => $get,
    	];

        view('appearance/sliders/detail/index', $vars);
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
        if ($id_taxonomy = input()->get('id_taxonomy')) {
            $this->presenter->page->setHeader( 'FORM_ADD' );
            models(Taxonomies::class)->appendColumns = [];
            $taxonomy = models(Taxonomies::class)->find($id_taxonomy);
            $vars = [
                'post' => new SplArrayObject(),
                'visibility' => visibilityOptions(),
                'status' => posts_status(),
                'taxonomy' => $taxonomy,
            ];

            $this->model->appendColumns = [
                'tag', 'record'
            ];

            if ($id) {
                if (false !== ($data = $this->model->find($id))) {
                    $this->presenter->page->setHeader( 'FORM_EDIT' );
                    $this->model->appendColumns = [
                        'tag', 'record', 'medias'
                    ];
                    
                    $vars['post'] = $data;
                } else {
                    $this->output->send(204);
                }
            }
            view('appearance/sliders/detail/form', $vars);
        } else {
            $this->output->send(404);
        }
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