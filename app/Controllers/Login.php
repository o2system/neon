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

namespace App\Controllers;

// ------------------------------------------------------------------------

use App\Http\Controller;

/**
 * Class Login
 *
 * @package App\Controllers
 */
class Login extends Controller
{
    /**
     * Login::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->theme->setLayout( 'login' );
        presenter()->page->setHeader( 'Login' );
    }

    // ------------------------------------------------------------------------

    /**
     * Login::index
     */
    public function index()
    {
        if (services('user')->loggedIn()) {
            redirect_url('dashboard');
        }
        view('auth/login');
    }

    // ------------------------------------------------------------------------

    /**
     * Login::authenticate
     */
    public function authenticate()
    {
        if ( $this->user->authenticate( $this->input->post( 'username' ), $this->input->post( 'password' ), $this->input->post( 'remember' ) ) ) {
            // Login True
            if($referer = session()->referer){
                unset(session()->referer);
                redirect_url($referer);
            }

            redirect_url('dashboard');
        } else {
            redirect_url( 'login' );
        }
    }

    public function logout()
    {
        services( 'user' )->logout();
        redirect_url('login');
    }

}