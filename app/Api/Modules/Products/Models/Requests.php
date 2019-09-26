<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 18/09/2019
 * Time: 11:22
 */

namespace App\Api\Modules\Products\Models;


use App\Api\Modules\Master\Models\Currencies;
use App\Api\Modules\Members\Models\Members;
use App\Api\Modules\Members\Models\Users;
use App\Api\Modules\Products\Models\Requests\Metadata;
use App\Api\Modules\Products\Models\Requests\Images;
use App\Api\Modules\System\Models\Modules\Users\Notifications;
use App\Api\Modules\Transactions\Models\Logs;
use App\Api\Modules\Transactions\Models\Transactions;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;

class Requests extends Model
{

    /**
     * Products::$table
     *
     * @var string
     */
    public $table = 'products_requests';

    /**
     * Products::$visibleColumns
     *
     * @var array
     */


    /**
     * Products::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'image',
        // 'metadata',
        // 'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Products::visibilityOptions
     *
     * @return array
     */
    public function visibilityOptions()
    {
        return [
            'PUBLIC' => language('PUBLIC'),
            'READONLY' => language('READONLY'),
            'PROTECTED' => language('PROTECTED'),
            'PRIVATE' => language('PRIVATE')
        ];
    }

    public function metadata($input = null)
    {
        if ($input) {
            $this->qb->where('name', $input);
        }
        if ($result = $this->hasMany(Metadata::class, 'id_product')) {
            $metadata = new SplArrayObject();
            foreach($result as $row) {
                if ($row->name == 'wholesale') {
                    continue;
                }
                $metadata->offsetSet($row->name, $row->content);
            }
            return $metadata;
        }
        return false;
    }

    public function images()
    {
        if ($datas = $this->hasMany(Images::class, 'id_product')) {
            $images = [];
            $no = 0;
            foreach ($datas as $data) {
                $filePath = PATH_STORAGE . 'images/products/'.$data->filename;
                if (is_file($filePath)) {
                    $images[$no++] = [
                        'name' => $data->metadata,
                        'image' => storage_url($filePath)
                    ];
                }
            }
            return $images;
        }
        return null;
    }

    public function image()
    {
        $rows = $this->row;
        if ($data = $this->hasOne(Images::class, 'id_product_request')) {
            $filePath = PATH_STORAGE . 'images/upload/'.$data->filename;
            if (is_file($filePath)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }elseif (is_file($filePath = PATH_STORAGE . 'images/upload/'.$rows->content)){
            return storage_url($filePath);
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function imagePrevious()
    {
        $rows = $this->row;
        if(is_file($filePath = PATH_STORAGE . 'images/upload/'.$rows->content)){
            return storage_url($filePath);
        }
        return storage_url('/images/default/no-image.jpg');
    }


    public function images_data()
    {
        $data = $this->hasMany(Images::class, 'id_product');
        if (count($data)) {
            return $data;
        }
        return null;
    }

    public function wholesales()
    {
        $this->qb->where('name', 'wholesale');
        if ($result = $this->hasOne(Metadata::class, 'id_product')) {
            $data = $result->content;
            return $result->content;
        }
        return false;
    }

    public function variants()
    {
        if ($data = $this->hasMany(Variants::class, 'id_product')) {
            return $data;
        }
        return false;
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'id_product_category');
    }

    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'id_currency');
    }

    public function insert(array $sets, $member)
    {
        if(is_array($sets)){
            if(parent::insert($sets)){
                $idProductRequest = $this->db->getLastInsertId();
                if(models(Transactions::class)->insert([
                    'number'    => models(Transactions::class)->getToken(),
                    'reference_id'  => $idProductRequest,
                    'reference_model'   =>  '\App\Api\Modules\Products\Models\Requests'
                ])){
                    $idTransaction = $this->db->getLastInsertId();
                    models(Logs::class)->insert([
                        'id_transaction' => $idTransaction,
                        'status'    => 'ON_REQUEST',
                        'timestamp' => timestamp(),
                        'expires'   => date('Y-m-d h:i:s', strtotime("+30 days")),
                    ]);
                    $user =  models(Users::class)->findWhere(['id_member' => $member->id],1);
                    models(Notifications::class)->insert([
                        'sys_module_user_sender_id' => $user->id_sys_user,
                        'sys_module_user_recipient_id' => 0,
                        'reference_id'  => $idTransaction,
                        'reference_model'   => 'App\Api\Modules\Transactions\Models\Transactions',
                        'message'   => 'mengajukan permintaan produk',
                        'metadata'  => 'ON_REQUEST'
                    ]);
                    models(\App\Api\Modules\Members\Models\Transactions::class)
                        ->insert([
                            'id_member' => $member->id,
                            'id_transaction'    => $idTransaction
                        ]);
                    return true;
                }
            }
        }
        return false;
    }


}