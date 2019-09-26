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

namespace App\Api\Modules\Companies\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Http\Controller;

/**
 * Class Contacts
 * @package App\Api\Modules\Companies\Controllers
 */
class Contacts extends Controller
{
    /**
     * Contacts::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_company',
            'label'    => 'Company id',
            'rules'    => 'required|integer',
            'messages' => 'Company ID cannot be empty and must be integer!',
        ],
        [
            'field'    => 'id',
            'label'    => 'Id',
            'rules'    => 'optional'
        ],
        [
            'field'    => 'name',
            'label'    => 'Contact Photo',
            'rules'    => 'required',
            'messages' => 'Company Contact cannot be empty and must be integer!',
        ],
        [
            'field'    => 'photo',
            'label'    => 'Contact Photo',
            'rules'    => 'optional',
            'messages' => 'Company Contact Photo cannot be empty and must be alphanumeric!',
        ],
        [
            'field'    => 'job_title',
            'label'    => 'Job Title',
            'rules'    => 'optional',
            'messages' => 'Contact Job title must be alphanumeric!',
        ],
        [
            'field'    => 'emails',
            'label'    => 'Contact Emails',
            'rules'    => 'optional|email',
            'messages' => 'Contact Emails must be emails!',
        ],
        [
            'field'    => 'messengers',
            'label'    => 'Messenger Contacts',
            'rules'    => 'optional',
            'messages' => 'Contact Messenger must be alphanumeric!',
        ],
        [
            'field'    => 'socials',
            'label'    => 'Socials Contact',
            'rules'    => 'optional',
            'messages' => 'Contact Social must be alphanumeric!',
        ]
    ];

    public function create()
    {
        $_POST['photo'] = null;
        $_POST['id'] = null;
        parent::create();
    }

    public function update()
    {
        if ($_POST['photo'] == null) {
            $_POST['photo'] = null;
        }
        parent::update();
    }
}
