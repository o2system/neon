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

namespace App\Api\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use App\Api\Modules\Company\Models\Company;
use App\Api\Modules\HumanResource\Models\Employee\Users;
use App\Api\Modules\System\Models\Modules\Users\Notifications;
use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;
use O2System\Security\Authentication\User\Account;
use O2System\Security\Authentication\User\Role;

/**
 * Class UserAuthentication
 *
 * @package App\Api\Http\AccessControl\Middleware
 */
class UserAuthentication implements RequestHandlerInterface
{
    /**
     * UserAuthentication::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request)
    {
        if( ! services( 'user' )->loggedIn() ) {
            redirect_url( 'login' );
        }else{
            $user = models(\App\Api\Modules\System\Models\Users::class)->find(session()->account['id']);
            $account = new Account($user->getArrayCopy());
            if($profile = $user->profile){
                $account->store('profile', $profile);
            }
            if($role = $user->role){
                $account->store('role', new Role([
                    'label' => $role->label,
                    'description' => $role->description,
                    'code' => $role->code,
                    'authorities' => $role->authorities
                ]));
            }
            if($employee = $user->employee){
                models(Notifications::class)->readNotifications($employee->id);
                $account->store('profile', $employee);
                $account->store('employee', $employee);
            }
            session()->set('account',$account->getArrayCopy());
            globals()->store('account', $account);
            globals()->store('company', models(Company::class)->getMetadata());
            presenter()->store('account', $account);
        }
    }
}