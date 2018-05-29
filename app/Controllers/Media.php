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

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Media
 * @package App\Controllers
 */
class Media extends AuthorizedController
{
    private $mediaModel;

    public function __construct()
    {
        $this->mediaModel = new \App\Models\Media();
    }

    public function index()
    {
        presenter()->page
            ->setHeader( 'Media' )
            ->setDescription( 'The O2CMS Media' );

        presenter()->page->icon->setClass( 'fas fa-image' );

        presenter()->page->breadcrumb->createList( new Link(
            language()->getLine( 'MEDIA' ),
            base_url( 'media' )
        ) );

        $media = $this->mediaModel->getuploadedImages();

        view( 'media', ['medias' => $media] );
    }

    public function form()
    {
        presenter()->theme->setLayout( 'add' );
        presenter()->page->setHeader( 'Add Media' );
        view( 'add-media' );
    }
}