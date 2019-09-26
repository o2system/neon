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

namespace App\Api\Modules\Pages\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Pages\Http\Controller;
use App\Api\Modules\Pages\Models\Pages as RealPages;
use App\Api\Modules\Pages\Models\Metadata;
use App\Api\Modules\Pages\Models\Settings;

/**
 * Class Pages
 * @package App\Api\Modules\Pages\Controllers
 */
class Pages extends Controller
{
    /**
     * Pages::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'id',
    //         'label'    => 'Id',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'title',
    //         'label'    => 'Title',
    //         'rules'    => 'required|alphanumericspaces',
    //         'messages' => 'Title cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
    //     ],
    //     [
    //         'field'    => 'slug',
    //         'label'    => 'Slug',
    //         'rules'    => 'required|alphadash',
    //         'messages' => 'Slug cannot be empty! the examples are like this = slug-slug-slug',
    //     ],
    //     [
    //         'field'    => 'content',
    //         'label'    => 'Content',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'visibility',
    //         'label'    => 'Visibility',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'start_publishing',
    //         'label'    => 'Start Publishing',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'finish_publishing',
    //         'label'    => 'Finish Publishing',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'meta',
    //         'label'    => 'meta',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'settings',
    //         'label'    => 'meta',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'record_status',
    //         'label'    => 'Status',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'id_taxonomy',
    //         'label'    => 'Status',
    //         'rules'    => 'optional',
    //     ],
    // ];

    public function create()
    {
        $_POST['id'] = null;
        $_POST['slug'] = str_replace(' ', '-', strtolower($_POST['slug']));
        parent::create();
    }

    public function trash()
    {
        if ($post = input()->post()) {
            if ($post->status == 'delete') {
                $id = $post->id;
                $meta_image = models(Metadata::class)->findWhere(['id_page' => $id, 'name' => 'photo']);
                if ($meta_image->count()) {
                    $meta_image = $meta_image->first();
                    if (is_file($filePath = PATH_STORAGE . 'images/pages/media/' . $meta_image->content)) {
                        unlink($filePath);
                    }
                }
                models(Metadata::class)->deleteManyBy(['id_page' => $id]);
                models(Settings::class)->deleteManyBy(['id_page' => $id]);
                if (models(RealPages::class)->delete($post->getArrayCopy())) {
                    $this->sendError(201, 'Successful delete request');
                } else {
                    $this->sendError(501, 'Failed delete request');
                }
            }            
        } else {
            $this->sendError(400);
        }
    }

    public function restore()
    {
        if ($post = input()->post()) {
            if ($post->status == 'restore') {
                $id = $post->id;
                $data = [];
                $data['record_status'] = 'DRAFT';
                $data['id'] = $id;
                $data['restore_status'] = 1;
                if (models(RealPages::class)->update($data)) {
                    $this->sendError(201, 'Successful restore request');
                } else {
                    $this->sendError(501, 'Failed restore request');
                }
            }
        } else {
            $this->sendError(400);
        }
    }

}
