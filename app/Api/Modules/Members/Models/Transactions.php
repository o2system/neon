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

namespace App\Api\Modules\Members\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use App\Api\Modules\Products\Models\Products;

/**
 * Class Transactions
 * @package App\Api\Modules\Members\Models
 */
class Transactions extends Model
{
    use RelationTrait;

    /**
     * Transactions::$table
     *
     * @var string
     */
    public $table = 'members_transactions';

    /**
     * Transactions::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_member',
        'id_transaction',
    ];

    /**
     * Transactions::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'member',
        'transaction'
    ];

    // ------------------------------------------------------------------------

    /**
     * Transactions::member
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member');
    }

    // ------------------------------------------------------------------------

    /**
     * Transactions::transaction
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Api\Modules\Transactions\Models\Transactions::class, 'id_transaction');
    }

    public function wishlists_members_all()
    {
        if (session()['account']['member']) {
            models(Products::class)->visibleColumns[]= 'logs_id';
            models(Products::class)->qb->select('products.*, transactions_logs.id AS logs_id');
            models(Products::class)->qb->join('transactions', 'transactions.reference_id = products.id');
            models(Products::class)->qb->join('transactions_logs', 'transactions_logs.id_transaction = transactions.id');
            models(Products::class)->qb->join('members_transactions', 'members_transactions.id_transaction = transactions.id');
            models(Products::class)->qb->where('members_transactions.id_member', session()['account']['member']['id']);
            models(Products::class)->qb->where('transactions_logs.status', 'ON_WISHLIST');
            models(Products::class)->qb->where('transactions.reference_model', '\App\Api\Modules\Products\Models\Products');
            $data = models(Products::class)->all();
            return $data;
        } else {
            return false;
        }
    }
}