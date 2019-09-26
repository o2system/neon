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

use App\Api\Modules\System\Models\Modules\Users\Notifications;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Logs
 * @package App\Api\Modules\Transactions\Models
 */
class Logs extends Model
{
    use RelationTrait;

    /**
     * Logs::$table
     *
     * @var string
     */
    public $table = 'transactions_logs';

    /**
     * Logs::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_transaction',
        'status',
        'timestamp',
        'expires'
    ];

    /**
     * Logs::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'transaction',
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::transaction
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'id_transaction');
    }

    public function insert(array $sets, $idUser = null)
    {
        parent::insert($sets);
        $transaction = models(Transactions::class)->find($sets['id_transaction']);
        switch ($sets['status']){
            case 'REQUEST_CONFIRM':
                $sender =  $transaction->memberTransaction->member->user->id;
                $recipient =  0;
                $message = 'member menanggapi permintaan produknya ';
                break;
            case 'CONFIRMED':
                $sender =  $transaction->company->user->id;
                $recipient =  $transaction->member->user->id;
                $message = 'admini menanggapi permintaan product anda ';
                break;
            case 'PAID':
                $sender =  $transaction->member->user->id;
                $recipient =  $transaction->company->user->id;
                $message = 'Member sudah membayar pembayaran pertama';
                break;
            case 'ON_DELIVERY':
                $sender =  $transaction->company->user->id;
                $recipient =  $transaction->member->user->id;
                $message = 'Admin sudah sedang mengantarkan product anda';
                break;
            case 'DELIVERED':
                $sender =   $transaction->member->user->id;
                $recipient =  $transaction->company->user->id;
                $message = 'Member mengkonfirmasi product yg dikirim';
                break;
        }
        models(Notifications::class)->insert([
            'sys_module_user_sender_id' => $sender,
            'sys_module_user_recipient_id' => $recipient,
            'reference_id'  => $transaction->id,
            'reference_model'   => 'App\Api\Modules\Transactions\Models\Transactions',
            'message'   => $message,
            'metadata'  => $sets['status']
        ]);
        return true;
    }

    public function total_sales()
    {
        if (globals()->account->company) {
            $this->qb->select('transactions_logs.*');
            $this->qb->join('transactions', 'transactions.id = transactions_logs.id_transaction');
            $this->qb->join('companies_products', 'companies_products.id_product = transactions.reference_id');
            $this->qb->where('transactions_logs.status', 'DELIVERED');
            $this->qb->where('transactions.reference_model', '\App\Api\Modules\Products\Models\Products');
            $this->qb->where('companies_products.id_company', globals()->account->company->id);
            if ($data = $this->all()) {
                if ($total = count($data)) {
                    return $total;
                }
            }
            return 0;            
        } else {
            return 0;
        }       
    }

    public function members_total_products()
    {
        if (globals()->account->member) {
            $this->qb->select('transactions_logs.*');
            $this->qb->join('transactions', 'transactions.id = transactions_logs.id_transaction');
            $this->qb->join('members_transactions', 'members_transactions.id_transaction = transactions.id');
            $this->qb->where('transactions_logs.status', 'DELIVERED');
            $this->qb->where('transactions.reference_model', '\App\Api\Modules\Products\Models\Products');
            $this->qb->where('members_transactions.id_member', globals()->account->member->id);
            $this->qb->groupBy('transactions_logs.id');
            if ($data = $this->all()) {
                if ($total = count($data)) {
                    return $total;
                }
            }
            return 0;            
        } else {
            return 0;
        }
    }
}