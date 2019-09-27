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

namespace App\Modules\Master\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Master\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Taxonomies\Models\Terms;
use App\Api\Modules\Taxonomies\Models\Metadata;

/**
 * Class Taxonomies
 * @package App\Modules\Taxonomies\Controllers
 */
class Taxonomies extends Controller
{
    public $model = '\App\Api\Modules\Taxonomies\Models\Taxonomies';
    public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

        if ($keyword = $get->keyword) {
            $this->model->qb->like('name', $keyword);
        }

        if ($get->entries) {
            if (is_numeric($get->entries)) {
                $entries = $get->entries;
            } else {
                $entries = null;
            }
        }

        if ($id_taxonomy_term = $get->id_taxonomy_term) {
            $all = (is_numeric($id_taxonomy_term) ? $this->model->findWhere(['id_taxonomy_term' => $id_taxonomy_term], $entries) : ($entries ? $this->model->allWithPaging(null, $entries) : $this->model->all()));
        } else {
            $all = $this->model->allWithPaging(null, $entries);
        }
        
        $vars = [
            'taxonomies' => $all,
            'terms' => models(Terms::class)->all(),
            'entries' => range(10, 100, 10),
            'get' => $get,
        ];

        view('taxonomies/index', $vars);
    }

    public function form($id=null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );
        models(Terms::class)->appendColumns = null;
        $vars = [
            'post' => new SplArrayObject(),
            'masterdata_status' => masterdata_status(),
            'terms' => models(Terms::class)->all(),
            'parents' => $this->model->all()
        ];

        if ($id) {
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                $vars['post'] = $data;
            } else {
                $this->output->send(204);
            }
        }

        if ($post = input()->post()) {
            if ($post->id) {
                if ($this->model->update($post->getArrayCopy())) {
                    return redirect_url('/master/taxonomies');
                }
            } else {
                if ($this->model->insert($post->getArrayCopy())) {
                    return redirect_url('/master/taxonomies');
                }
            }
        }

        view('taxonomies/form', $vars);
    }

    public function delete($id)
    {
        if ($id) {
            $data = $this->model->find($id);
            if ($data) {
                if ($data->metadata('photo')) {
                    $filePath = PATH_STORAGE . 'images/master/taxonomies/';
                    if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
                        unlink($image);
                    }
                }
            }
            models(Metadata::class)->deleteManyBy(['id_taxonomy' => $id]);
            if ($this->model->delete($id)) {
                redirect_url(input()->server('HTTP_REFERER'));
            }
        }
    }
}