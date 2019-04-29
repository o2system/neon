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

namespace App\Api\Http;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
use O2System\Framework\Models\Sql\Model;
use O2System\Security\Filters\Rules;

/**
 * Class Controller
 * @package App\Api\Http
 */
class Controller extends Restful
{
    /**
     * Controller::$model
     *
     * @var Model
     */
    public $model;

    /**
     * Controller::$params
     *
     * @var array
     */
    public $params = [];

    /**
     * Controller::$rules
     *
     * @var array
     */
    public $rules = [];

    /**
     * Controller::$fields
     *
     * @var array
     */
    public $fields = [];

    // ------------------------------------------------------------------------

    /**
     * Controller::__construct
     */
    public function __reconstruct()
    {
        parent::__construct();

        if (empty($this->model)) {
            $controllerClassName = get_called_class();
            $modelClassName = str_replace('Controllers', 'Models', $controllerClassName);

            if (class_exists($modelClassName)) {
                $this->model = new $modelClassName();
            }
        } elseif (class_exists($this->model)) {
            $this->model = new $this->model();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::index
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function index()
    {
        if ( ! $this->model instanceof Model) {
            $this->sendError(503, 'Model is not ready');
        }

        if (count($this->params)) {
            if ($get = input()->get()) {
                $rules = new Rules($get);
                $rules->sets($this->rules);

                if ( ! $rules->validate()) {
                    $this->sendError(400, implode(', ', $rules->getErrors()));
                }
            } else {
                $this->sendError(400, 'Get parameters cannot be empty!');
            }

            $conditions = $get->getArrayCopy();

            if (false !== ($result = $this->model->withPaging()->findWhere($conditions))) {
                if ($result->count()) {
                    $this->sendPayload($result);
                } else {
                    $this->sendError(204);
                }
            } else {
                $this->sendError(204);
            }
        } elseif ($get = input()->get()) {
            if (false !== ($result = $this->model->withPaging()->findWhere($get->getArrayCopy()))) {
                if ($result->count()) {
                    $this->sendPayload($result);
                } else {
                    $this->sendError(204);
                }
            } else {
                $this->sendError(204);
            }
        } else {
            if (false !== ($result = $this->model->allWithPaging())) {
                $this->sendPayload($result);
            } else {
                $this->sendError(204);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::create
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function create()
    {
        if ($post = input()->post()) {
            if (count($this->rules)) {
                $rules = new Rules($post);
                $rules->sets($this->rules);
                if ( ! $rules->validate()) {
                    $this->sendError(400, implode(', ', $rules->getErrors()));
                }
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            $data = [];

            if (count($this->fields)) {
                foreach ($this->fields as $field) {
                    if ($post->offsetExists($field)) {
                        $data[ $field ] = $post->offsetGet($field);
                    }
                }
            }

            if (count($data)) {
                $data[ 'record_create_timestamp' ] = $data[ 'record_update_timestamp' ] = timestamp();
                $data[ 'record_create_user' ] = $data[ 'record_update_user' ] = globals()->account->id;

                if ($this->model->insert($data)) {
                    $data['id'] = $this->model->db->getLastInsertId();
                    $this->sendPayload([
                        'code' => 201,
                        'Successful insert request',
                        'data' => $data
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

    // ------------------------------------------------------------------------

    /**
     * Controller::update
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function update()
    {
        if ($post = input()->post()) {
            if (count($this->rules)) {
                $rules = new Rules($post);
                $rules->sets($this->rules);
                $rules->add('id', 'ID', 'required', 'ID field cannot be empty!');

                if ( ! $rules->validate()) {
                    $this->sendError(400, implode(', ', $rules->getErrors()));
                }
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            $data = [];

            if (count($this->fields)) {
                foreach ($this->fields as $field) {
                    if ($post->offsetExists($field)) {
                        $data[ $field ] = $post->offsetGet($field);
                    }
                }
            }

            if (count($data)) {
                $data[ 'record_update_timestamp' ] = timestamp();
                $data[ 'record_update_user' ] = globals()->account->id;

                if ($this->model->update($data)) {
                    $this->sendError(201, 'Successful update request');
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

    // ------------------------------------------------------------------------

    /**
     * Controller::delete
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function delete()
    {
        if ($post = input()->post()) {
            $rules = new Rules($post);
            $rules->add('id', 'ID', 'required', 'ID field cannot be empty!');

            if ( ! $rules->validate()) {
                $this->sendError(400, implode(', ', $rules->getErrors()));
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($this->model->delete($post->id)) {
                $this->sendError(201, 'Successful delete request');
            } else {
                $this->sendError(501, 'Failed delete request');
            }
        } else {
            $this->sendError(400);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::publish
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function publish()
    {
        if ($post = input()->post()) {
            $rules = new Rules($post);
            $rules->add('id', 'ID', 'required', 'ID field cannot be empty!');

            if ( ! $rules->validate()) {
                $this->sendError(400, implode(', ', $rules->getErrors()));
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($this->model->publish($post->id)) {
                $this->sendError(201, 'Successful publish request');
            } else {
                $this->sendError(501, 'Failed publish request');
            }
        } else {
            $this->sendError(400);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::unpublish
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function unpublish()
    {
        if ($post = input()->post()) {
            $rules = new Rules($post);
            $rules->add('id', 'ID', 'required', 'ID field cannot be empty!');

            if ( ! $rules->validate()) {
                $this->sendError(400, implode(', ', $rules->getErrors()));
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($this->model->unpublish($post->id)) {
                $this->sendError(201, 'Successful unpublish request');
            } else {
                $this->sendError(501, 'Failed unpublish request');
            }
        } else {
            $this->sendError(400);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::archive
     *
     * @throws \O2System\Spl\Exceptions\Logic\OutOfRangeException
     */
    public function archive()
    {
        if ($post = input()->post()) {
            $rules = new Rules($post);
            $rules->add('id', 'ID', 'required', 'ID field cannot be empty!');

            if ( ! $rules->validate()) {
                $this->sendError(400, implode(', ', $rules->getErrors()));
            }

            if ( ! $this->model instanceof Model) {
                $this->sendError(503, 'Model is not ready');
            }

            if ($this->model->unpublish($post->id)) {
                $this->sendError(201, 'Successful archived request');
            } else {
                $this->sendError(501, 'Failed archived request');
            }
        } else {
            $this->sendError(400);
        }
    }
}
