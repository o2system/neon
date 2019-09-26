<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace App\Manage\Modules\Testimonials\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Testimonials\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Testimonials
 * @package App\Manage\Modules\Testimonials\Controllers
 */
class Testimonials extends Controller
{
    public $model = '\App\Api\Modules\Testimonials\Models\Testimonials';

    public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        $vars = [
            'testimonials' => $this->model->filter($get),
            'get' => $get,
            'entries' => range(10, 100, 10),
        ];
        view('testimonials/index', $vars);
    }

    public function edit($id = null)
    {
        if ($id) {
            $data = $this->model->find($id);
            $data->record_status = $data->record_status == "UNPUBLISH" ? "PUBLISH" : "UNPUBLISH";
            $this->model->update(['record_status' => $data->record_status], ['id' => $data->id]);
            redirect_url('testimonials');
        } else {
            $this->output->send(204);
        }
    }

    public function delete($id = null)
    {
        if ($id) {
            $this->model->delete($id);
            redirect_url('testimonials');
        }else{
            $this->output->send(204);
        }

    }

}
