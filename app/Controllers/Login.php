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

use App\Http\Controller;
use O2System\Cache\Item;
use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\Libraries\Email;
use O2System\Kernel\Http\Message\Uri;
use O2System\Security\Generators\Token;

/**
 * Class Login
 *
 * @package App\Controllers
 */
class Login extends Controller
{
    public function __reconstruct()
    {
        parent::__reconstruct();
        presenter()->theme->setLayout( 'login' );
    }

    /**
     * Login::index
     */
    public function index()
    {
        if( $this->user->loggedIn() ) {
            redirect_url( 'stats' );
        }

        presenter()->page->setHeader( 'Login' );
        view( 'login' );
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::register
     */
    public function register()
    {
        /*$account = new Acl\Datastructures\Account(
            [
                'email'    => 'administrator@circle-creative.com',
                'msisdn'   => '085280790088',
                'username' => 'administrator',
                'password' => 'administrator123!',
                'pin'      => '123456789',
                'role'     => 1,
                'profile'  => [
                    'name' => [
                        'first'   => 'Circle',
                        'last'    => 'Creative',
                        'display' => 'Circle Creative',
                    ],
                ],
            ]
        );

        $this->user->register( $account );*/

        presenter()->page->setHeader( 'Register' );
        view( 'register' );
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::authenticate
     */
    public function authenticate()
    {
        if ( $this->user->login( $this->input->post( 'username' ), $this->input->post( 'password' ), $this->input->post( 'remember' ) ) ) {
            // Login True
            redirect_url('stats');
        } else {
            redirect_url( 'login' );
        }
    }

    public function forgotPassword() {
        presenter()->page->setHeader( 'Forgot Password' );

        if (null !== ($username = input()->post('username'))) {
            if(false !== ($account = services('user')->findAccount($username))) {
                $token = Token::generate(10, Token::ALPHANUMERIC_STRING);
                if(cache()->hasItem($token)) {
                    $token = Token::generate(10, Token::ALPHANUMERIC_STRING);
                }

                $cache = new Item($token, $account->username, 3600);
                cache()->save($cache);

                $email = new Email();
                $email->subject('RESET_PASSWORD');
                $email->from('noreply@' . (new Uri())->getHost());
                $email->to('steeven.lim@gmail.com');
                $email->template('email/reset-password', [
                   'username' => $account->username,
                   'token' => $token
                ]);

                if($email->send()) {
                    session()->setFlash('success', 'Silakan Check Email Anda');
                }
            } else {
                session()->setFlash('failed', sprintf('%s tidak ditemukan', $username));
            }
        }

        view( 'password/forgot' );
    }

    public function resetPassword($token = null) {
        if($post = input()->post()) {
            if(services('user')->update(new Account(
                [
                    'username' => $post->username,
                    'password' => $post->password,
                ]
            ))) {
                cache()->deleteItem($token);
                view( 'password/success' );
            } else {
                view( 'password/error' );
            }
        } else {
            if(isset($token) && cache()->hasItem($token)) {
                $cache = cache()->getItem($token);
                if(false !== ($account = services('user')->findAccount($cache->get()))) {
                    view( 'password/reset', ['account' => $account] );
                }
            } else {
                session()->setFlash('error', 'Token yang anda masukan salah atau sudah tidak berlaku');
                view( 'password/forgot' );
            }
        }

    }
}