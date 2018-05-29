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

namespace App\Http;

// ------------------------------------------------------------------------

use App\Http\Presenter;

/**
 * Class Controller
 *
 * @package App\Http
 */
class Controller extends \O2System\Framework\Http\Controller
{
    /**
     * Controller::__construct
     */
    public function __reconstruct()
    {
        $this->presenter->meta->title->prepend( 'O2System Webapp' );
        $this->presenter->meta->offsetSet( 'copyright', 'Copyright (c) 2017 - Steeve Andrian' );
        $this->language->loadFile( 'app' );

        $this->presenter->store( 'app', modules()->current()->getProperties() );
        $this->presenter->store( 'page', new Presenter\Page() );
        $this->presenter->store( 'menus', new Presenter\Menus() );
        $this->presenter->store( 'settings', new Presenter\Settings() );

        if( $this->user->loggedIn() ) {
            $account = $this->user->getAccount();
            $account->roles = $this->user->getRoles();
            $account->access = $this->user->getRolesAccess();
            $account->profile = $this->user->getProfile();

            foreach( $account->profile->images->getArrayCopy() as $offset => $image ) {
                $filePath = PATH_STORAGE . 'users' . DIRECTORY_SEPARATOR . $account->username . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $image;
                if( is_file( $filePath ) ) {
                    $account->profile->images->store( $offset, storage_url( $filePath ) );
                } else {
                    $account->profile->images->store( $offset, assets_url( 'img' . DIRECTORY_SEPARATOR . $offset . '.jpg' ) );
                }
            }

            $this->presenter->store( 'account', $account );

        }
    }
}