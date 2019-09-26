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
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Security\Generators\Token;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Transactions
 * @package App\Api\Modules\Transactions\Models
 */
class Transactions extends Model
{
    use RelationTrait;

    /**
     * Transactions::$table
     *
     * @var string
     */
    public $table = 'transactions';

    /**
     * Transactions::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'number',
        'reference_model',
        'reference_id'
    ];

    /**
     * Transactions::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'metadata',
        'product',
        'creditor'
    ];

    // ------------------------------------------------------------------------

    /**
     * Transactions::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata()
    {
        if($result = $this->hasMany(Metadata::class, 'id_transaction')) {
            $metadata = new SplArrayObject();
            foreach($result as $row) {
                $metadata->offsetSet($row->name, $row->content);
            }
            return $metadata;
        }

        return null;
    }

    public function chats()
    {
        return $this->hasMany(Chats::class, 'id_transaction');
    }



    public function imgDownPayment()
    {
        $this->qb->where('name', 'confirm');
        if($metadata =  $this->metadata()){
            if(is_file($pathFile = PATH_STORAGE.'images/upload/'.$metadata->confirm)){
                return images_url($pathFile);
            }else{
                return images_url('default/no-image.jpg');
            }
        }
        return false;
    }

    public function shipping()
    {
        $this->qb->where('name', 'shipping');
        if($metadata =  $this->metadata()){
            return $metadata->shipping->send;
        }
        return false;
    }

    public function installment()
    {
        $this->qb->where('name', 'installment');
        if($metadata =  $this->metadata()){
            return $metadata->installment;
        }
        return false;
    }
    public function postal_fee()
    {
        $this->qb->where('name', 'postal_fee');
        if($metadata =  $this->metadata()){
            return $metadata->postal_fee;
        }
        return false;
    }


    public function creditor()
    {
        $this->qb->where('name', 'id_creditor');
        if($creditor =  $this->metadata()){
            return models(Companies::class)->find($creditor->id_creditor);
        }
        return false;

    }

    // ------------------------------------------------------------------------

    /**
     * Transactions::logs
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function logs()
    {
        return $this->hasMany(Logs::class, 'id_transaction');
    }

    public function statistics()
    {
        $result = new SplArrayObject();
        $status = [
            'ON_REQUEST' => 'fas fa-money-bill-alt',
            'ON_REQUEST_CONFIRM' => 'fas fa-money-bill-alt',
            'REQUEST_CONFIRM' => 'fas fa-money-bill-alt',
            'ON_WISHLIST' => 'fas fa-money-bill-alt',
            'ON_SHOPPING_CART' => 'fas fa-money-bill-alt',
            'WAITING_FOR_CONFIRMATION' => 'fas fa-money-bill-alt',
            'CONFIRMED' => 'fas fa-money-bill-alt',
            'WAITING_FOR_APPROVAL' => 'fas fa-money-bill-alt',
            'APPROVED' => 'fas fa-money-bill-alt',
            'DECLINED' => 'fas fa-money-bill-alt',
            'DELIVERED' => 'fas fa-money-bill-alt',
            'PAID' => 'fas fa-money-bill-alt',
            'CANCELED_BY_USER' => 'fas fa-money-bill-alt',
            'CANCELED_BY_SYSTEM' => 'fas fa-money-bill-alt',
            'ON_DELIVERY' => 'fas fa-money-bill-alt',
            'REQUEST_DECLINED' => 'fas fa-money-bill-alt'
        ];
        $no = 0;
        foreach ($status as $key => $value){
                $status = $this->qb->select('transactions_logs.*')->from('transactions_logs')
                ->join('transactions', 'transactions.id = transactions_logs.id_transaction')
                ->where('transactions_logs.status', $key)
                ->groupBy('transactions_logs.id')
                ->get();
            $result->offsetSet($no++, new SplArrayObject([
                'status' => $key,
                'total' => count($status),
                'icon' => $value
            ]));
        }
        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Transactions::latestLog
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function latestLog()
    {
        $this->qb->orderBy('transactions_logs.id', 'DESC');
        return $this->hasOne(Logs::class, 'id_transaction');
    }

    public function product()
    {
        return $this->belongsTo(new $this->row->reference_model(), 'reference_id');
    }

    public function getToken()
    {
        $token = (new Token())->generate(8, 4);
        if($data = $this->findWhere([
            'number'    => $token
        ])){
            if ( ! count($data)) {
                return $token;
            }
        }
        $this->getToken();
    }

    public function addCart($post, $member)
    {
        if($post->id_product){
            if(parent::insert([
                'reference_id'   => $post->id_product,
                'reference_model'    => '\App\Api\Modules\Products\Models\Products',
                'number'    => $this->getToken()
            ])){
                $idTransaction = $this->db->getLastInsertId();
                models(Logs::class)->insert([
                    'id_transaction' => $idTransaction,
                    'status'    => $post->status,
                    'timestamp' => timestamp(),
                    'expires'   => date('Y-m-d h:i:s', strtotime("+30 days")),
                ]);
                models(\App\Api\Modules\Members\Models\Transactions::class)
                    ->insert([
                        'id_transaction' => $idTransaction,
                        'id_member'  => $member->id
                    ]);
                return true;
            }
        }
    }

    public function addWishlist($post, $member)
    {
        if($post->id_product){
            if(parent::insert([
                'reference_id'   => $post->id_product,
                'reference_model'    => '\App\Api\Modules\Products\Models\Products',
                'number'    => $this->getToken()
            ])){
                $idTransaction = $this->db->getLastInsertId();
                models(Logs::class)->insert([
                    'id_transaction' => $idTransaction,
                    'status'    => $post->status,
                    'timestamp' => timestamp(),
                    'expires'   => date('Y-m-d h:i:s', strtotime("+30 days")),
                ]);
                models(\App\Api\Modules\Members\Models\Transactions::class)
                    ->insert([
                        'id_transaction' => $idTransaction,
                        'id_member'  => $member->id
                    ]);
                return true;
            } else {
                $this->sendError(501, 'Wishlist Failed');
            }
        }
    }

    public function updateWishlist($post)
    {
        if($post->id_log){
            if(models(Logs::class)->update([
                'status' => $post->status
            ], [
                'id' => $post->id_log
            ])){
                return true;
            } else {
                $this->sendError(501, 'Update Wishlist Failed');
            }
        }
    }

    public function readTransaction(array $transactions)
    {
        $dataTransaction = $transactions;
        foreach ($transactions as $key =>  $transaction){
            if($transaction['checking']){
                unset($dataTransaction[$key]['checking']);
                $dataTransaction[$key]['transaction'] = parent::find($transaction['id_transaction']);
                $dataTransaction[$key]['creditor'] =  models(Companies::class)->find($transaction['id_creditor']);
            }else{
                unset($dataTransaction[$key]);
            }
        }
        return $dataTransaction;
    }

    public function memberTransaction()
    {
        return $this->hasOne(\App\Api\Modules\Members\Models\Transactions::class, 'id_transaction');
    }

    public function companyTransaction()
    {
        return $this->hasOne(\App\Api\Modules\Companies\Models\Transactions::class, 'id_transaction');
    }

    public function company()
    {
        if($company = $this->companyTransaction()){
            return $company->company;
        }
        return false;
    }


    public function member()
    {
        if($member = $this->memberTransaction()){
            return $member->member;
        }
        return false;
    }


    public function transactions_filter($get, $id_company)
    {
        $this->qb->select('transactions.*');
        $this->qb->join('products', 'products.id = transactions.reference_id');
        $this->qb->join('companies_products', 'companies_products.id_product = products.id');
        $this->qb->where('companies_products.id_company', $id_company);
        $this->qb->where('transactions.reference_model', '\App\Api\Modules\Products\Models\Products');

        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('products.name', $keyword);
        }

        $this->qb->groupBy('transactions.id');

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging();
        }

        return $all;
    }

    public function filter($get)
    {
        $this->qb->select('transactions.*');
        $this->qb->join('members_transactions', 'members_transactions.id_transaction = transactions.id');
        if ($get) {
            $this->qb->join('products', 'products.id = transactions.reference_id');
            $this->qb->join('products_requests', 'products_requests.id = transactions.reference_id');
            
            if ($get->period) {
                $time_data = explode('-', str_replace(' ', '', $get->period));
                $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
                $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
            }

            if ($keyword = $get->keyword) {
                $this->qb->like('products.name', $keyword);
                $this->qb->orLike('products_requests.name', $keyword);
            }
        }

        $this->qb->groupBy('transactions.id');
        $this->qb->orderBy('id', 'DESC');

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging();
        }

        return $all;
    }

    public function companies_filter($get)
    {
        if ($company = globals()->account->company) {
            $this->qb->select('transactions.*')
            ->join('companies_transactions', 'companies_transactions.id_transaction = transactions.id')
            ->where('companies_transactions.id_company', $company->id);

            if ($get) {
                $this->qb->join('products', 'products.id = transactions.reference_id');
                $this->qb->join('products_requests', 'products_requests.id = transactions.reference_id');
                
                if ($get->period) {
                    $time_data = explode('-', str_replace(' ', '', $get->period));
                    $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
                    $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
                }

                if ($keyword = $get->keyword) {
                    $this->qb->like('products.name', $keyword);
                    $this->qb->orLike('products_requests.name', $keyword);
                }
            }

            $this->qb->groupBy('transactions.id');
            $this->qb->orderBy('id', 'DESC');

            if ($get->entries) {
                $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
            } else {
                $all = $this->allWithPaging();
            }

            return $all;
        }
    }

}
