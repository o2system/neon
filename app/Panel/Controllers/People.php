<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Spl\DataStructures\SplArrayStorage;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class People
 * @package App\Panel\Controllers
 */
class People extends AuthorizedController
{
    /**
     * People::$model
     * @var string|\App\Models\People
     */
    public $model = 'App\Models\People';

    // ------------------------------------------------------------------------

    /**
     * People::index
     */
    public function index()
    {
        if ($keyword = input()->get('keyword')) {
            $this->model->qb->like('fullname', $keyword);
        }

        $people = $this->model->all();

        view('people/index', ['peoples' => $people]);
    }

    // ------------------------------------------------------------------------

    /**
     * People::form
     */
    public function form($id = null)
    {
        $people = $this->model->find($id);

        if ($post = input()->post()) {
            if ($id == null) {
                $this->model->insert($post);
                redirect_url('system/people');
            } else {
                $this->model->update($post);
                redirect_url('system/people');
            }
        }

        view('people/form', ['people' => $people]);
    }
    // ------------------------------------------------------------------------

    /**
     * People::detail
     */
    public function detail($id)
    {
        $people = $this->model->find($id);

        view('people/detail', ['people' => $people]);
    }
    // ------------------------------------------------------------------------

    /**
     * People::delete
     */
    public function delete($id)
    {
        if ($this->model->softDelete($id)) {
            redirect_url('system/people');
        }
    }
}
