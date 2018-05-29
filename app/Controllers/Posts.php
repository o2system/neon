<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */

// ------------------------------------------------------------------------

namespace App\Controllers;

// --------------------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;
use App\Libraries\Form\Settings;
use App\Models\Sections;
use O2System\Framework\Libraries\Ui\Components\Pagination;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Posts
 *
 * @package App\Controllers
 */
class Posts extends AuthorizedController
{
    public function __reconstruct()
    {
        parent::__reconstruct();
        presenter()->page
            ->setHeader('Posts')
            ->setDescription('Manage Posts');
    }

    /**
     * Posts::index
     *
     * @return void
     */
    public function index()
    {
        presenter()->page->icon->setClass('fas fa-file-code');

        presenter()->page->breadcrumb->createList(new Link(
            language()->getLine('POSTS'),
            base_url('posts')
        ));

        models()->controller->qb->where('id_section', 1);

        if ($status = input()->get('status')) {
            models()->controller->qb->where('record_status', $status);
        } else {
            models()->controller->qb->whereIn('record_status', ['PUBLISH', 'UNPUBLISH']);
        }

        if($query = input()->get('q')) {
            models()->controller->qb
                ->like('title', $query)
                ->orLike('content', $query);
        }

        $vars = [];
        $entries = input()->get('entries');
        $entries = empty($entries) ? 10 : $entries;

        $posts = models()->controller->page(null, input()->get('page'), $entries);

        if ($posts) {
            $info = $posts->getInfo();

            $pagination = new Pagination($info->getTotal('rows'), $entries);
            $pagination->attributes->addAttributeClass('justify-content-center');

            $vars = [
                'posts'      => $posts,
                'pagination' => $pagination,
            ];
        }

        view('posts/index', $vars);
    }

    public function form($idPost = null)
    {
        presenter()->page->setHeader('Form');

        $idPost = empty($idPost) ? input()->get('copy') : $idPost;
        $idPost = intval($idPost);

        $vars[ 'fields' ] = new Settings();

        $sections = new Sections();
        $section = $sections->find(1);
        $vars['categories'] = $section->categories;

        models()->controller->find($idPost);

        if ($post = models()->controller->find($idPost)) {
            if ($idPost = input()->get('copy')) {
                unset($post->id);
                $post->title = $post->title . ' (Copy)';
                $post->segments = $post->segments . '-copy';
            }

            presenter()->page->setHeader($post->title);

            $vars[ 'post' ] = $post;
        } elseif ($post = input()->post()) {
            if (empty($post->id)) {
                models()->controller->insert($post->getArrayCopy());
                session()->setFlash('success', 'Successful insert new post: ' . $post->title);
            } else {
                models()->controller->update($post->getArrayCopy(), ['id' => $post->id]);
                session()->setFlash('success', 'Successful update post: ' . $post->title);
            }

            redirect_url('posts');
        }

        view('posts/form', $vars);
    }

    public function publish($idPost)
    {
        $idPost = intval($idPost);

        if (false !== ($post = models()->controller->find($idPost))) {
            if (models()->controller->publish($idPost)) {
                session()->setFlash('success', 'Successful publish <strong>' . $post->title . '</strong> post.');
            } else {
                session()->setFlash('error', 'Failed to publish <strong>' . $post->title . '</strong> post.');
            }
        }

        redirect_url('posts');
    }

    public function trash($idPost)
    {
        $idPost = intval($idPost);

        if (false !== ($post = models()->controller->find($idPost))) {
            if (models()->controller->trash($idPost)) {
                session()->setFlash('success', 'Successful moved <strong>' . $post->title . '</strong> post to trash.');
            } else {
                session()->setFlash('error', 'Failed to move <strong>' . $post->title . '</strong> post to trash.');
            }
        }

        redirect_url('posts');
    }

    public function restore($idPost)
    {
        $idPost = intval($idPost);

        if (false !== ($post = models()->controller->find($idPost))) {
            if (models()->controller->restore($idPost)) {
                session()->setFlash('success', 'Successful restore <strong>' . $post->title . '</strong> post.');
            } else {
                session()->setFlash('error', 'Failed to restore <strong>' . $post->title . '</strong> post.');
            }
        }

        redirect_url('posts');
    }

    public function delete($idPost)
    {
        $idPost = intval($idPost);

        if (false !== ($post = models()->controller->find($idPost))) {
            if (models()->controller->delete($idPost)) {
                session()->setFlash('success', 'Successful delete <strong>' . $post->title . '</strong> post.');
            } else {
                session()->setFlash('error', 'Failed to delete <strong>' . $post->title . '</strong> post.');
            }
        }

        redirect_url('posts');
    }
}