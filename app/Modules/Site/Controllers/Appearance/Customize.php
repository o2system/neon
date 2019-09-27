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

namespace App\Modules\Site\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Modules\Site\Http\Controller;

/**
 * Class Customizer
 * @package App\Modules\Sites\Controllers
 */
class Customize extends Controller
{

    /**
     * @var \App\Api\Modules\Master\Models\Themes
     */
    public $model = '\App\Api\Modules\Master\Models\Themes';

    public function __reconstruct()
    {
        parent::__reconstruct();
        presenter()->theme->setLayout( 'customizer' );
    }
	public function index()
    {
        $current = $this->model->getCurrent();

        $vars = [
            'fonts' => ['Roboto', 'Do Hyeon', 'Open Sans', 'Raleway', 'Poppins'],
            'schemes' => ['Orange', 'Blue', 'Black'],
            'current' => $current
        ];

        // Define menu index.
        $vars['menu_index'] = 0;

        if (isset($vars['options']['menu'])) {
            $vars['menu_index'] = count($vars['options']['menu']);
        }

        if ($post = $this->input->post()) {
            foreach ($post as $field => $value){
                $this->model->insertOrUpdate([
                    'name'  => $field,
                    'content'   => $value
                ],[
                    'name'  => $field
                ]);
            }

            $this->session->setFlash('success', 'Successfully updated!');
            redirect_url($_SERVER['HTTP_REFERER']);
        }


        view('appearance/customizer/index', $vars);
    }
}