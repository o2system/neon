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

namespace App\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\Controller;
use O2System\Security\Authentication\JsonWebToken;

/**
 * Class Login
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

        presenter()->theme->setLayout('login');
    }

    // ------------------------------------------------------------------------

    /**
     * Login::index
     */
    public function index()
    {
//        services('accessControl')->forceLogin('developer');
//        redirect_url('dashboard');

        if (services('accessControl')->loggedIn()) {
            if ($refererUrl = input()->server('HTTP_REFERER')) {
                redirect_url($refererUrl);
            } else {
                redirect_url('dashboard');
            }
        }

        view('auth/login');
    }

    // ------------------------------------------------------------------------

    /**
     * Login::logout
     */
    public function logout()
    {
        presenter()->page->setHeader('Logout');

        services('accessControl')->logout();

        view('logout');
    }

    // ------------------------------------------------------------------------

    /**
     * Login::authenticate
     */
    public function authenticate()
    {
        if($post = input()->post()) {
            $post->validation([
                'username' => 'required',
                'password' => 'required'
            ], [
                'username' => [
                    'required' => 'Username cannot be empty!'
                ],
                'password' => [
                    'required' => 'Password cannot be empty!'
                ]
            ]);

            if($post->validate()) {
                if(services()->has('accessControl')) {

                    if(services('accessControl')->authenticate($post->username, $post->password)) {
                        if(is_ajax()) {
                            $jwt = new JsonWebToken();
                            $token = $jwt->encode(session()->get('account'));

                            if(services('user')->loggedIn()) {
                                output()->sendPayload($token);
                            } else {
                                output()->sendPayload([
                                    'success' => false,
                                    'message' => 'Login failed, please try again in a few minutes!'
                                ]);
                            }
                        } else {
                            redirect_url('dashboard');
                        }
                    } else {
                        session()->setFlash('danger', 'Wrong username or password');

                        redirect_url('login');
                    }
                } else {
                    output()->sendError(503, 'Access Control service is not exists!');
                }
            } else {
                output()->sendError(400, 'Username and password cannot be empty!');
            }
        } else {
            output()->sendError(400);
        }
    }
}
