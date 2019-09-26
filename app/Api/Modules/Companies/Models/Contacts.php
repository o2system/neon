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

namespace App\Api\Modules\Companies\Models;
// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Contacts
 * @package App\Api\Modules\Companies\Models
 */
class Contacts extends Model
{
    /**
     * Contacts::$table
     *
     * @var string
     */
    public $table = 'companies_contacts';

    /**
     * Contacts::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_company',
        'name',
        'photo',
        'job_title',
        'emails',
        'mobiles',
        'socials',
        'messengers'
    ];

    public $appendColumns = [
        'companies'
    ];

    // ------------------------------------------------------------------------

    /**
     * Contacts::companies
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function companies()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }

    public function image()
    {
        $row = $this->row;
        if ($row) {
            if (is_file($filePath = PATH_STORAGE . 'images/companies/contacts/' . $row->photo)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function insert($post)
    {
        if ($post) {
            if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/companies/contacts/';
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
                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['photo'] = $filename;
                }
            }
            $redirect_url = domain_url('/companies/overview/contacts/'.$post['id_company'], null, 'manage');
            if (parent::insert($post)) {
                return redirect_url($redirect_url);
            } else {
                return redirect_url($redirect_url);
            }
        }
    }

    public function update($post, $conditions)
    {
        if ($post) {
            if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/companies/contacts/';
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
                    if ($post->id) {
                        $data = $this->find($post->id);
                        if (is_file($image = $filePath.$data->photo)) {
                            unlink($image);
                        }
                    }
                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['photo'] = $filename;
                }
            }

            $redirect_url = domain_url('/companies/overview/contacts/'.$post['id_company'], null, 'manage');
            if (parent::update($post, $conditions)) {
                return redirect_url($redirect_url);
            } else {
                return redirect_url($redirect_url);
            }
        }
    }
}
