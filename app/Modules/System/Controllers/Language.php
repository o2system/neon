<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 21/09/2019
 * Time: 13:15
 */

namespace App\Manage\Modules\System\Controllers;


use App\Manage\Modules\System\Http\Controller;

class Language extends Controller
{
    public function index()
    {

        $language = $this->input->get('language');

        // Set Lang

        // Setup Lang
        $this->language->setDefault($language);

        redirect_url($this->input->server('HTTP_REFERER'));
    }
}