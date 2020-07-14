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

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Models\Options;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Posts
 * @package App\Panel\Controllers
 */
class Posts extends AuthorizedController
{
    /**
     * Posts::$model
     * 
     * @var string|\App\Models\Posts
     */
    public $model = 'App\Models\Posts';

    // ------------------------------------------------------------------------

    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->offsetSet('mediaBrowser', function(){
            view('components/dialog/media', [
                'medias' => models(\O2System\Framework\Models\Sql\System\Media::class)->allWithPaging(),
                'space' => new SplArrayObject([
                    'total' => $spaceTotal = disk_total_space(PATH_STORAGE),
                    'used' => $spaceUsed = (new SplDirectoryInfo(PATH_STORAGE))->getSize(),
                    'percentage' => round(($spaceUsed / $spaceTotal) * 100, 5)
                ])
            ], true);
        });
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::index
     */
    public function index()
    {
        $this->model->visibleRecordStatus = [ input()->get('status', 'PUBLISH') ];
        
        $this->model->qb
            ->where([
                'record_language' => config()->language['default'],
            ])
            ->whereIn('record_type', ['ARTICLE', 'VIDEO', 'AUDIO', 'GALLERY']);
        
        if ($keyword = input()->get('keyword')) {
            $this->model->qb->like('title', $keyword);
        }

        view('posts/index', [
            'posts' => $this->model->all()
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::form
     *
     * @param int $id
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function form($id = null)
    {
        $id = intval($id);

        $vars = [
            'post' => new SplArrayObject([
                'record' => new SplArrayObject([
                    'visibility' => language('PUBLIC'),
                    'status' => language('PUBLISH')
                ]),
                'metadata' => new SplArrayObject([
                    'robot' => 'index, follow',
                    'embed_code' => null,
                    'featured' => 'NO',
                    'show_sharing_button' => 'YES',
                    'show_like_button' => 'YES',
                    'allow_comments' => 'YES',
                    'allow_pingback_trackback' => 'YES'
                ]),
                'tags' => []
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
                ],
                'categories' => null,
            ])
        ];

        if (! empty($id)) {
            $recordLanguage = input()->get('record_language', 'en-US');
            
            if ($result = $this->model->findWhere([
                'id' => $id,
                'record_language' => $recordLanguage
            ])) {
                if($result) {
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
                        redirect_url('posts');
                    }
                    
                    break;

                case 'UPDATE':
                    if ($this->model->update($post, [
                        'id' => $post->id,
                        'record_language' => $post->record_language
                    ])) {
                        redirect_url($_SERVER['HTTP_REFERER']);
                    }

                    break;
            }
        }
        
        if (empty($vars['post']->metadata)) {
            $vars['post']->metadata = new SplArrayObject([
                'robot' => 'index, follow'
            ]);
        }

        view('posts/form', $vars);
    }
    // ------------------------------------------------------------------------

    /**
     * Posts::publish
     * @param $id
     */
    public function publish($id)
    {
        $this->model->publish($id);
        redirect_url('posts');
    }
    // ------------------------------------------------------------------------

    /**
     * Posts::unpublish
     * @param $id
     */
    public function unpublish($id)
    {
        $this->model->unpublish($id);
        redirect_url('posts');
    }
    // ------------------------------------------------------------------------

    /**
     * Posts::archive
     * @param $id
     */
    public function archive($id)
    {
        $this->model->archive($id);
        redirect_url('posts');
    }
    // ------------------------------------------------------------------------

    /**
     * Posts::delete
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

        redirect_url('posts');
    }
}
