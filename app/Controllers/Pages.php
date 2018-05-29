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
use O2System\Framework\Http\Router\Datastructures\Page;
use O2System\Framework\Libraries\Ui\Components\Pagination;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Image\Uploader;

/**
 * Class Pages
 *
 * @package App\Controllers
 */
class Pages extends AuthorizedController
{
    /**
     * Pages Page
     *
     * @var Page
     */
    protected $page;

    // ------------------------------------------------------------------------

    /**
     * Pages::setPage
     *
     * @param \O2System\Framework\Http\Router\Datastructures\Page $page
     *
     * @return void
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        if (false !== ($settings = $this->page->getSettings())) {
            if ($settings->offsetExists('theme')) {
                presenter()->setTheme($settings->theme);
            }

            if ($settings->offsetExists('title')) {
                presenter()->meta->title->append($settings->title);
            }

            if ($settings->offsetExists('pageTitle')) {
                presenter()->meta->title->append($settings->pageTitle);
            }

            if ($settings->offsetExists('browserTitle')) {
                presenter()->meta->title->replace($settings->browserTitle);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::index
     *
     * @return void
     */
    public function index()
    {
        presenter()->page->setHeader('Pages');

        if ( ! empty($this->page)) {
            view()->page($this->page);
        } else {
            presenter()->page
                ->setHeader('Pages')
                ->setDescription('Manage Pages');

            presenter()->page->icon->setClass('fas fa-file-code');

            presenter()->page->breadcrumb->createList(new Link(
                language()->getLine('PAGES'),
                base_url('pages')
            ));

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

            $pages = models()->controller->page(null, input()->get('page'), $entries);

            if ($pages) {
                $info = $pages->getInfo();

                $pagination = new Pagination($info->getTotal('rows'), $entries);
                $pagination->attributes->addAttributeClass('justify-content-center');

                $vars = [
                    'pages'      => $pages,
                    'pagination' => $pagination,
                ];
            }

            view('pages/index', $vars);
        }
    }

    public function form($idPage = null)
    {
        $idPage = empty($idPage) ? input()->get('copy') : $idPage;
        $idPage = intval($idPage);

        presenter()->theme->setLayout('form-page');

        $vars[ 'fields' ] = new Settings();

        if ($page = models()->controller->find($idPage)) {
            if ($idPage = input()->get('copy')) {
                unset($page->id);
                $page->title = $page->title . ' (Copy)';
                $page->segments = $page->segments . '-copy';
            }

            $vars[ 'page' ] = $page;
        } elseif ($post = input()->post()) {
            if ($image = input()->files('file-image')) {
                $upload = new Uploader();
                $upload->process('file-image');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }

                    session()->setFlash('error', 'Failed to upload image' . $errors);
                } else {
                    $post->image = $upload->getUploadedFiles()->first();
                }
            }

            if (empty($post->id)) {
                models()->controller->insert($post->getArrayCopy());
                session()->setFlash('success', 'Successful insert new page: ' . $post->title);
            } else {
                models()->controller->update($post->getArrayCopy(), ['id' => $post->id]);
                session()->setFlash('success', 'Successful update page: ' . $post->title);
            }

            redirect_url('pages');
        }

        view('pages/form', $vars);
    }

    public function publish($idPage)
    {
        $idPage = intval($idPage);

        if (false !== ($page = models()->controller->find($idPage))) {
            if (models()->controller->publish($idPage)) {
                session()->setFlash('success', 'Successful publish <strong>' . $page->title . '</strong> page.');
            } else {
                session()->setFlash('error', 'Failed to publish <strong>' . $page->title . '</strong> page.');
            }
        }

        redirect_url('pages');
    }

    public function trash($idPage)
    {
        $idPage = intval($idPage);

        if (false !== ($page = models()->controller->find($idPage))) {
            if (models()->controller->trash($idPage)) {
                session()->setFlash('success', 'Successful moved <strong>' . $page->title . '</strong> page to trash.');
            } else {
                session()->setFlash('error', 'Failed to move <strong>' . $page->title . '</strong> page to trash.');
            }
        }

        redirect_url('pages');
    }

    public function restore($idPage)
    {
        $idPage = intval($idPage);

        if (false !== ($page = models()->controller->find($idPage))) {
            if (models()->controller->restore($idPage)) {
                session()->setFlash('success', 'Successful restore <strong>' . $page->title . '</strong> page.');
            } else {
                session()->setFlash('error', 'Failed to restore <strong>' . $page->title . '</strong> page.');
            }
        }

        redirect_url('pages');
    }

    public function delete($idPage)
    {
        $idPage = intval($idPage);

        if (false !== ($page = models()->controller->find($idPage))) {
            if (models()->controller->delete($idPage)) {
                session()->setFlash('success', 'Successful delete <strong>' . $page->title . '</strong> page.');
            } else {
                session()->setFlash('error', 'Failed to delete <strong>' . $page->title . '</strong> page.');
            }
        }

        redirect_url('pages');
    }
}