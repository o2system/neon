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

namespace App\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\Uri;
use O2System\Framework\Libraries\Ui\Components;
use O2System\Framework\Libraries\Ui\Contents;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Page
 *
 * @package App\Datastructures
 */
class Page extends AbstractRepository
{
    /**
     * Page::__construct
     */
    public function __construct()
    {
        // Create Page breadcrumbs
        $breadcrumb = new Components\Breadcrumb();
        $breadcrumb->createList( new Contents\Link( language()->getLine( 'STATS' ), base_url( 'stats' ) ) );
        $this->store( 'breadcrumb', $breadcrumb );

        // Create Page menus
        $this->store('menus', new Menus() );

        // Create Page icon
        $this->store( 'icon', new Contents\Icon( 'fas fa-archive' ) );

        // Create Page layout
        $this->store('layout', 'layout');

        // Store Page Uri
        $uri = new Uri();
        if ( $uri->__toString() === base_url() ) {
            $uri->addPath( 'stats' );
        }

        $this->store( 'uri', $uri );
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setHeader
     *
     * @param string $header
     *
     * @return static
     */
    public function setHeader( $header )
    {
        $header = trim( $header );
        $this->store( 'header', $header, true );
        presenter()->meta->offsetSet( 'subtitle', $header );
        presenter()->meta->title->append( $header );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setSubHeader
     *
     * @param string $subHeader
     *
     * @return static
     */
    public function setSubHeader( $subHeader )
    {
        $this->store( 'subHeader', trim( $subHeader ), true );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setDescription
     *
     * @param string $description
     *
     * @return static
     */
    public function setDescription( $description )
    {
        $description = trim( $description );
        $this->store( 'description', $description, true );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setIcon
     *
     * @param string $icon
     *
     * @return static
     */
    public function setIcon( $icon )
    {
        $this->store( 'icon', new Contents\Icon( $icon ), true );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::setContent
     *
     * @param string $content
     *
     * @return static
     */
    public function setContent( $content )
    {
        $content = trim( $content );
        $this->store( 'content', $content, true );

        return $this;
    }

    /**
     * Page::setLayout
     *
     * @param string $layout
     *
     * @return static
     */
    public function setLayout( $layout )
    {
        $this->store('layout', $layout, true );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::view
     *
     * @param string $view
     * @param array  $vars
     */
    public function view( $view = null, array $vars = null )
    {
        if(isset($view)) {
            $this->setContent(
                view()->load($view, $vars, true)
            );
        }

        view()->load($this->offsetGet('layout'));
    }
}