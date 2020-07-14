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

namespace App\Panel\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Panel\Controllers\Posts as Controller;

/**
 * Class Sliders
 * @package App\Panel\Controllers
 */
class Sliders extends Controller
{
    /**
     * Sliders::index
     */
    public function index()
    {
        $this->model->visibleRecordStatus = [ input()->get('status', 'PUBLISH') ];

        $this->model->qb
            ->where([
                'record_language' => config()->language['default'],
            ])
            ->whereIn('record_type', ['SLIDER']);

        if ($keyword = input()->get('keyword')) {
            $this->model->qb->like('title', $keyword);
        }

        view('appearance/sliders/index', [
            'posts' => $this->model->all()
        ]);
    }

    /**
     * @param int|null $id
     */
    public function form($id = null)
    {
        view('appearance/sliders/form', [
            'posts' => $this->model->all()
        ]);
    }
}
