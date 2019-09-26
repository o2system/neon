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

namespace App\Api\Modules\Transactions\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Transactions\Http\Controller;

/**
 * Class Metadata
 * @package App\Api\Modules\Transactions\Controllers
 */
class Metadata extends Controller
{
    /**
     * Metadata::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_transaction',
            'label'    => 'ID Transaction',
            'rules'    => 'required|integer',
            'messages' => 'ID Transaction cannot be empty and must be integer',
        ],
        [
            'field'    => 'name',
            'label'    => 'Name',
            'rules'    => 'required',
            'messages' => 'Name cannot be empty!',
        ],
        [
            'field'    => 'content',
            'label'    => 'Content',
            'rules'    => 'optional',
        ],
    ];

    public function checkout()
    {
        if($post = input()->post()){
            if(($transactions = $post->transaction) && ($shipping = $post->shipping)) {
                if ($this->model->checkout($transactions, $shipping, $post->payment_method)) {
                    $this->sendPayload([
                        'message' => 'insert success'
                    ]);
                }
            }

        }else{
            $this->sendError(400, 'request post');
        }

    }


}
