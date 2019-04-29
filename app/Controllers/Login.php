<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Controllers;

// --------------------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\PublicController;
use O2System\Security\Generators\Token;
use O2System\Framework\Libraries\Email;

/**
 * Class Login
 * @package App\Controllers
 */
class Login extends PublicController
{
    /**
     * Login::index
     */
    public function index()
    {
        if (services('user')->loggedIn()) {
            redirect_url('personal/agenda');
        }

        presenter()->theme->setLayout('login');
        presenter()->page->setHeader('Login');
        view('login');
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::authenticate
     */
    public function authenticate()
    {
        $login = services('user')->authenticate(input()->post('username'), input()->post('password'));
        if ($login) {
            redirect_url('personal/agenda');
        } else {
            session()->setFlash('danger', language('ALERT_DANGER_LOGIN_FAILED'));
            redirect_url('login');
        }
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::forgotPassword
     *
     * @throws \Exception
     */
    public function forgotPassword()
    {
        $this->presenter->theme->setLayout('login');
        $this->presenter->page->setHeader('Forgot Password');

        $userNameOrEmail = $this->input->post('username');

        if ($userNameOrEmail !== null) {
            $checkUserOrEmail = $this->models->users->findwhere([
                'email' => $userNameOrEmail,
            ]);
            if ($checkUserOrEmail == false) {
                $checkUserOrEmail = $this->models->users->findwhere([
                    'username' => $userNameOrEmail,
                ]);
            }
            if ($checkUserOrEmail == false) {
                session()->setFlash('error', $this->language->getLine('NOT_EXIST_EMAIL_OR_USERNAME'));
            } else {
                $token = new Token();
                $getToken = $token->generate(8);
                $arrayToken = ['pin' => $getToken];
                session()->setFlash('success', $this->language->getLine('ALERT_CHECK_YOUR_EMAIL'));
                $accountInfo = $checkUserOrEmail->first();
                $this->models->users->update($arrayToken, ['id' => $accountInfo->id]);

                $email = new Email();
                $email->subject('Reset Password');
                $email->from('no-reply@selarasholding.com', 'Selaras Holding');
                $email->to($accountInfo->email);
                $email->template('email/reset-password', [
                    'username' => $accountInfo->username,
                    'token' => $getToken,
                ]);
                if ($email->send()) {
                } else {
                }
            }
        }
        view('forgot-password');
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::validate
     *
     * @param string $token
     */
    public function validate($token = null)
    {
        $this->presenter->theme->setLayout('login');
        $this->presenter->page->setHeader('Reset Password');
        $account = $this->models->users->findWhere(['pin' => $token]);
        if ($post = $this->input->post()) {
            $password = $post->password;
            $repasword = $post->repassword;
            if ($password == $repasword) {
                session()->setFlash('success', language('SUCCESS_CHANGE_YOUR_PASSWORD'));
                $this->models->users->update(['password' => $this->user->passwordHash($password)], ['id' => $post->id]);
                redirect_url('login');
            } else {
                session()->setFlash('failed', language('Correct Your Password'));
            }
        }
        if ($account != null) {
            view('reset-account', [
                'username' => $account->first()->username,
                'iduser' => $account->first()->id,
            ]);
        } else {
            redirect_url('forgot-password');
        }
    }

    // --------------------------------------------------------------------------------------

    /**
     * Login::reset
     */
    public function reset()
    {
        $newPassword = $this->input->post('password');
        $idUser = $this->input->post('id_user');

        if ($newPassword !== false && $idUser !== false) {
            if ($this->getForgotModel()->resetAccountPassword($idUser, $newPassword)) {
                redirect_url('/login');
            } else {
                print_out('Failed');
            }
        }
    }
}
