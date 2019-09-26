<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 20/09/2019
 * Time: 20:09
 */

namespace App\Manage\Modules\System\Controllers;


use App\Api\Modules\System\Models\Modules\Users\Notifications;
use App\Manage\Modules\System\Http\Controller;

class Users extends Controller
{
    public function notifications()
    {
        if($id = input()->get('id')){
            $notification = models(Notifications::class)->find($id);
            models(Notifications::class)->update([
               'id' => $id,
               'status' => 'SEEN'
            ]);
            switch ($notification->metadata){
                case 'ON_REQUEST':
                        redirect_url(base_url('transactions/confirm-request',['id' => $notification->reference_id]));
                    break;
                case 'PAID':
                    redirect_url(base_url('transactions/approval-down-payment',['id' => $notification->reference_id]));
                    break;
                case 'WAITING_FOR_CONFIRMATION':
                    redirect_url(base_url('transactions/approval',['id' => $notification->reference_id]));
                    break;
                case 'CONFIRMED':
                    redirect_url(base_url('members/transactions/paid-down-payment',['id'=>$notification->reference_id]));
                    break;
                case 'ON_REQUEST_CONFIRM':
                    redirect_url(base_url('members/transactions/request-confirm',['id' => $notification->reference_id]));
                    break;
                case 'ON_DELIVERY':
                    redirect_url(base_url('members/transactions/confirm-delivery',['id'=>$notification->reference_id]));
                    break;
                case 'DELIVERED':
                    redirect_url(base_url('transactions/detail',['id'=>$notification->reference_id]));
                    break;
            }
        }
    }
}