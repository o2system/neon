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

namespace App\Modules\Site\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Site\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Media\Models\Media;

/**
 * Class Pages
 * @package App\Modules\Sites\Controllers
 */
class Page extends Controller
{
    public $model = '\App\Api\Modules\Pages\Models\Pages';
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
            'pages' => $all,
            'get' => $get
        ];
        view('pages/index', $vars);
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
            'pages' => $all,
            'get' => $get
        ];
        view('pages/drafts', $vars);
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
            'pages' => $all,
            'get' => $get
        ];
        view('pages/trash', $vars);
    }

    public function form($id=null)
    {
        // models(PagesMedia::class)->appendColumns = ['images'];
        // if ($medias = models(PagesMedia::class)->findWhere(['id_post' => 0])) {
        //     foreach ($medias as $media) {
        //         if ($image = $media->images) {
        //             if (is_file($filePath = PATH_STORAGE . 'images/posts/media/' . $image->filename)) {
        //                 unlink($filePath);
        //             }
        //             models(Media::class)->delete($image->id);
        //         }
        //     }
        //     models(PagesMedia::class)->deleteManyBy(['id_post' => 0]);
        // }
        $this->presenter->page->setHeader( 'FORM_ADD' );
        $vars = [
            'post' => new SplArrayObject(),
            'visibility' => visibilityOptions(),
            'status' => posts_status(),
            'world_languages' => world_languages()
        ];

        $this->model->appendColumns = [
            'metadata', 'record', 'image'
        ];

        if ($id) {
            $this->model->qb->where('record_status !=', 'DELETED');
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                $vars['post'] = $data;
                // print_out($vars);
            } else {
                $this->output->send(204);
            }
        }

        view('pages/form', $vars);
    }
}