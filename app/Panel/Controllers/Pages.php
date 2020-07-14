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

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayStorage;
use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Models\Options;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Pages
 * @package App\Panel\Controllers
 */
class Pages extends AuthorizedController
{
    /**
     * Pages::$model
     *
     * @var string|\App\Models\Posts
     */
    public $model = 'App\Models\Posts';

    // ------------------------------------------------------------------------
    
    /**
     * Pages::index
     */
    public function index()
    {
        if(presenter()->page->file instanceof SplFileInfo) {
            view()->page(presenter()->page->file->getRealPath());
        } else {
            $this->model->visibleRecordStatus = [ input()->get('status', 'PUBLISH') ];

            $this->model->qb
                ->where([
                    'record_language' => config()->language['default'],
                ])
                ->whereIn('record_type', 'PAGE');

            if ($keyword = input()->get('keyword')) {
                $this->model->qb->like('title', $keyword);
            }

            $pages = $this->model->all();

            view('pages/index', [
                'pages' => $pages
            ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::form
     * @param null $id
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function form($id = null)
    {
        $vars = [
            'post' => new SplArrayObject([
                'blocks' => new SplArrayObject(),
                'record' => new SplArrayObject([
                    'visibility' => 'PUBLIC',
                    'status' => 'PUBLISH'
                ]),
                'metadata' => new SplArrayObject([
                    'robot' => 'index, follow'
                ]),
                'settings' => new SplArrayObject([
                    'featured' => 'NO',
                    'show_sharing_button' => 'YES',
                    'show_like_button' => 'YES',
                    'allow_comments' => 'YES',
                    'allow_pingback_trackback' => 'YES'
                ]),
                'tags' => [],
                'media' => []
            ]),
            'options' => new SplArrayObject([
                'languages' => models(Options::class)->languages(),
                'types' => [
                    'ARTICLE' => language('ARTICLE'),
                    'VIDEO' => language('VIDEO'),
                    'AUDIO' => language('AUDIO'),
                ],
                'status' => models(Options::class)->status(),
                'visibilities' => models(Options::class)->visibilities(),
                'metarobots' => [
                    'index, follow' => 'Index, Follow',
                    'noindex, follow' => 'No Index, Follow',
                    'index, nofollow' => 'Index, No Follow',
                    'noindex, nofollow' => 'No Index, No Follow'
                ]
            ])
        ];

        if (! empty($id)) {
            $recordLanguage = input()->get('record_language', 'en-US');

            if ($result = $this->model->findWhere([
                'id' => $id,
                'record_type' => 'PAGE',
                'record_language' => $recordLanguage
            ])) {
                if($result->count()) {
                    $vars['post'] = $result->first();
                } else {
                    redirect_url('error/404');
                }
            }
        }

        if ($post = input()->post()) {
            $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';
            
            // Enabled Flash Message
            $this->model->flashMessage(true);

            switch ($action) {
                case 'INSERT':
                    if ($this->model->insert($post)) {
                        redirect_url('pages');
                    }

                    break;

                case 'UPDATE':

                    if ($this->model->updateOrInsert($post, [
                        'id' => $post->id,
                        'record_language' => $post->record_language
                    ])) {
                        redirect_url('pages');
                    }

                    break;
            }
        }

        if (empty($vars['post']->metadata)) {
            $vars['post']->metadata = new SplArrayObject([
                'robot' => 'index, follow'
            ]);
        }

        if (empty($vars['post']->blocks)) {
            $vars['post']->blocks = new SplArrayObject();
        }

        view('pages/form', $vars);
    }
    // ------------------------------------------------------------------------

    /**
     * Pages::publish
     * @param $id
     */
    public function publish($id)
    {
        $this->model->publish($id);
        redirect_url('pages');
    }
    // ------------------------------------------------------------------------

    /**
     * Pages::unpublish
     * @param $id
     */
    public function unpublish($id)
    {
        $this->model->unpublish($id);
        redirect_url('pages');
    }
    // ------------------------------------------------------------------------

    /**
     * Pages::archive
     * @param $id
     */
    public function archive($id)
    {
        $this->model->archive($id);
        redirect_url('pages');
    }
    // ------------------------------------------------------------------------

    /**
     * Pages::delete
     * @param $id
     */
    public function delete($id)
    {
        if($page = $this->model->find($id, 1)) {
            if($page->record->status === 'DELETED') {
                $this->model->delete($id);
            } else {
                $this->model->softDelete($id);
            }
        }

        redirect_url('pages');
    }
}
