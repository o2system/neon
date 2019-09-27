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

namespace App\Modules\Companies\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Companies\Http\Controller;
use App\Api\Modules\Companies\Models\Metadata;
use App\Api\Modules\Master\Models\Geodirectories;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use App\Api\Modules\Taxonomies\Models\Terms;
use App\Api\Modules\Companies\Models\Categories;
use App\Libraries\Rajaongkir;

/**
 * Class Companies
 * @package App\Modules\Companies\Controllers
 */
class Companies extends Controller
{
    /**
     * @var string
     */
    public $model = '\App\Api\Modules\Companies\Models\Companies';

    /**
     *
     */
    public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

        $all = $this->model->filter($get);
        $vars = [
            'entries' => range(10, 100, 10),
            'get' => $get,
            'companies' => $all
        ];

        view('companies/index', $vars);
    }

    /**
     *
     */
    public function table()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

        $all = $this->model->filter($get);
        $vars = [
            'entries' => range(10, 100, 10),
            'get' => $get,
            'companies' => $all
        ];
        view('companies/table', $vars);
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );
        $term = models(Terms::class)->find('classifications', 'slug');
        $rajaongkir = new Rajaongkir();
        $cities = $rajaongkir->result->getCities();
        $vars = [
            'post' => new SplArrayObject(),
            'cities'    => $cities,
            'taxonomies' => ($term ? $term->taxonomies : null),
            'companies_categories' => companies_categories()
        ];

        $this->model->appendColumns = [
            'metadata'
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
            $post = $post->getArrayCopy();
            if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/companies/';
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
                    if ($post['id']) {
                        $data = $this->model->find($post['id']);
                        if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
                            unlink($image);
                        }
                    }

                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['meta']['photo'] = $filename;
                }
            }


            if ($id) {
                if ($this->model->update($post)) {
                    redirect_url('/companies');
                }
            } else {
                if ($this->model->insert($post)) {
                    redirect_url('/companies');
                }
            }
        }

        view('companies/form', $vars);
    }

    /**
     * @param null $id
     */
    public function overview($id = null)
    {
        if ($id) {
            $this->model->appendColumns = [
                'metadata', 'city'
            ];
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'PAGE_DETAIL' );
                $vars['company'] = $data;
                view('detail', $vars);
            } else {
                $this->output->send(204);
            }
        }
    }

    /**
     * @param null $id
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete($id = null)
    {
        if ($id) {
            $data = $this->model->find($id);
            $filePath = PATH_STORAGE . 'images/companies/';
            if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
                unlink($image);
            }
            models(Metadata::class)->deleteManyBy(['id_company' => $id]);
            if ($this->model->delete($id)) {
                redirect_url('/companies');
            }
            redirect_url('/companies');
        } else {
            $this->output->send(404);
        }
    }
}
