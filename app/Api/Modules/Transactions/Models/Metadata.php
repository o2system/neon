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

namespace App\Api\Modules\Transactions\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Models\Companies;
use App\Api\Modules\Interests\Models\Interests;
use App\Api\Modules\System\Models\Modules\Users\Notifications;
use App\Libraries\Rajaongkir;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Metadata
 * @package App\Api\Modules\Transactions\Models
 */
class Metadata extends Model
{
    use RelationTrait;

    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'transactions_metadata';

    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_transaction',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'creditor',
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::transaction
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'id_transaction');
    }

    public function checkout($transactions, $shipping, $paymentMethod)
    {
        $rajaOngkir = new Rajaongkir();
        if(is_array($transactions)){
            foreach ($transactions as $key => $data) {
                foreach ($data as $name => $content) {
                    if ($name !== 'id_transaction') {
                        parent::insert([
                            'name' => $name,
                            'content' => $content,
                            'id_transaction' => $data['id_transaction']
                        ]);
                    }
                }
                parent::insert([
                    'id_transaction' => $data['id_transaction'],
                    'name'  => 'shipping',
                    'content'   => $shipping
                ]);


                parent::insert([
                    'id_transaction' => $data['id_transaction'],
                    'name'  => 'payment_method',
                    'content'   => $paymentMethod
                ]);
                //insert per transaction to creditor

                models(\App\Api\Modules\Companies\Models\Transactions::class)
                    ->insert([
                        'id_transaction'    => $data['id_transaction'],
                        'id_company'    => $data['id_creditor'],
                    ]);
                //change status transaction
                models(Logs::class)->insert([
                   'status' => 'WAITING_FOR_CONFIRMATION',
                   'id_transaction' => $data['id_transaction'],
                    'timestamp' => timestamp(),
                    'expires'   => date('Y-m-d h:i:s', strtotime("+30 days")),
                ]);
                $transaction = models(Transactions::class)->find($data['id_transaction']);
                models(Notifications::class)->insert([
                    'sys_module_user_sender_id' => $transaction->member->user->id,
                    'sys_module_user_recipient_id' => $transaction->company->user->id,
                    'reference_id'  => $data['id_transaction'],
                    'reference_model'   => 'App\Api\Modules\Transactions\Models\Transactions',
                    'message'   => 'member menunggu confirmasi checkout ',
                    'metadata'  => 'WAITING_FOR_CONFIRMATION'
                ]);
                $weight = $transaction->product->metadata->weight_value;
                $origin = $transaction->product->company->id_geodirectory;
                $cost = $rajaOngkir->result->getCost(['city' => $origin],['city' => $shipping['send']['city']],$weight,$data['courier']);
                parent::insert([
                    'id_transaction' => $data['id_transaction'],
                    'name'  => 'postal_fee',
                    'content'   => $cost
                ]);
                if($getInterestCompany = models(Interests::class)->findWhere([
                    'id_company'    => $data['id_creditor'],
                    'id_product_category'    => $transaction->product->id_product_category,
                ], 1)){
                    $loanAmount = $transaction->product->price_sale - $data['down_payment'];
                    $calculate = calculate_monthly_sliding_rate_installments($loanAmount,$getInterestCompany->interest,$data['tenor']);
                }else{
                    $calculate = [
                      'message' => 'company credit no have category this'
                    ];
                }
                parent::insert([
                    'id_transaction' => $data['id_transaction'],
                    'name'  => 'installment',
                    'content'   => $calculate
                ]);

            }
            return true;
        }
        return false;
    }
}