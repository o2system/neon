<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 17/09/2019
 * Time: 15:32
 */

namespace App\Api\Modules\Transactions\Controllers;



use App\Api\Modules\Transactions\Http\Controller;
use O2System\Framework\Models\Sql\Model;
use O2System\Security\Filters\Rules;

class Logs extends Controller
{


    public $model = '\App\Api\Modules\Transactions\Models\Logs';

    public function create()
    {
        if ($post = input()->post()) {
            if (count($this->fillableColumnsWithRules)) {
                $rules = new Rules($post);
                $rules->sets($this->fillableColumnsWithRules);
                if ( ! $rules->validate()) {
                    $this->sendError(400, $rules->displayErrors(true));
                }
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            $data = [];

            if (count($this->fillableColumnsWithRules)) {
                foreach ($this->fillableColumnsWithRules as $column) {
                    if ($post->offsetExists($column[ 'field' ])) {
                        $data[ $column[ 'field' ] ] = $post->offsetGet($column[ 'field' ]);
                    }
                }
            } elseif (count($this->fillableColumns)) {
                foreach ($this->fillableColumns as $column) {
                    if ($post->offsetExists($column[ 'field' ])) {
                        $data[ $column[ 'field' ] ] = $post->offsetGet($column[ 'field' ]);
                    }
                }
            } else {
                $data = $post->getArrayCopy();
            }

            if (count($data)) {
                $data[ 'record_create_timestamp' ] = $data[ 'record_update_timestamp' ] = timestamp();
                $data[ 'record_create_user' ] = $data[ 'record_update_user' ] = globals()->account->id;

                if ($this->model->insert($data, globals()->account->member->user->id)) {
                    $data[ 'id' ] = $this->model->db->getLastInsertId();
                    $this->sendPayload([
                        'code' => 201,
                        'Successful insert request',
                        'data' => $data,
                    ]);
                } else {
                    $this->sendError(501, 'Failed update request');
                }
            } else {
                $this->sendError(400, 'Post parameters cannot be empty!');
            }
        } else {
            $this->sendError(400);
        }
    }
}