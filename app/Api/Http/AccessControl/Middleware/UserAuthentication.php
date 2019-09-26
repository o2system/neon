<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace App\Api\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;
use O2System\Security\Authentication\User\Account;

/**
 * Class UserAuthentication
 *
 * @package App\Http\AccessControl\Middleware
 */
class UserAuthentication implements RequestHandlerInterface
{
    /**
     * UserAuthentication::$user
     *
     * @var Account
     */
    protected $account;

    // ------------------------------------------------------------------------

    /**
     * UserAuthentication::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request)
    {
        if($user = models('users')->find(session()['account']['id'])){
            $this->account = new Account($user->getArrayCopy());
            $this->account->store('user', $user);

            if ($profile = $user->profile) {
                $this->account->store('profile', $profile);
            }

            if($member = $user->member){
                $this->account->store('member', $member);
            }

            session()->set('account', $this->account->getArrayCopy());
            globals()->store('account', $this->account);
            presenter()->store('account', $this->account);
        }
    }
}
