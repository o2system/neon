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

namespace App\Api\Modules\Companies\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Master\Models\Geodirectories;
use App\Api\Modules\Members\Models\Members as RealMembers;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Security\Generators\Uid;
use App\Api\Modules\Products\Models\Products as RealProducts;
use App\Api\Modules\System\Models\Users as SysUsers;
use App\Libraries\Rajaongkir;


/**
 * Class Companies
 * @package App\Api\Modules\Companies\Models
 */
class Companies extends Model
{
    /**
     * Companies::$table
     *
     * @var string
     */
    public $table = 'companies';

    /**
     * Companies::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_company_category',
        'code',
        'name',
        'id_geodirectory'

    ];

    public $appendColumns = [
        'metadata'
    ];

    // ------------------------------------------------------------------------

    /**
     * Companies::category
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function category()
    {
        return $this->belongsTo(Categories::class, 'id_company_category');
    }

    // ------------------------------------------------------------------------

    public function creditors()
    {
        $this->qb->where('id_company_category', 2);
        return $this->all();
    }

    public function merchants()
    {
        $this->qb->where('id_company_category', 1);
        return $this->all();
    }

    /**
     * Companies::geodirectory
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function geodirectory()
    {
        return $this->belongsTo(Geodirectories::class, 'id_geodirectory');
    }

    // ------------------------------------------------------------------------

    /**
     * Companies::contacts
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function contacts()
    {
        return $this->hasMany(Contacts::class, 'id_company');
    }

    public function contacts_filter($get)
    {
        $this->qb->where('id_company', $this->row->id);
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('name', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? models(Contacts::class)->allWithPaging(null, $get->entries) : models(Contacts::class)->all());
        } else {
            $all = models(Contacts::class)->allWithPaging();
        }

        return $all;
    }

    public function members_filter($get, $id_company)
    {
        $this->qb->select('members.*');
        $this->qb->join('companies_members', 'companies_members.id_member = members.id');
        $this->qb->where('companies_members.id_company', $id_company);
        
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(members.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(members.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('members.fullname', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? models(RealMembers::class)->allWithPaging(null, $get->entries) : models(RealMembers::class)->all());
        } else {
            $all = models(RealMembers::class)->allWithPaging();
        }

        return $all;
    }

    public function users_filter($get, $id_company)
    {
        $this->qb->select('sys_users.*');
        $this->qb->join('companies_users', 'companies_users.id_sys_user = sys_users.id');
        $this->qb->where('companies_users.id_company', $id_company);

        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(sys_users.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(sys_users.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('sys_users.username', $keyword);
        }
        $this->qb->groupBy('sys_users.id');
        if ($get->entries) {
            $all = (is_numeric($get->entries) ? models(SysUsers::class)->allWithPaging(null, $get->entries) : models(SysUsers::class)->all());
        } else {
            $all = models(SysUsers::class)->allWithPaging();
        }

        return $all;
    }

    public function products_filter($get)
    {
        $this->qb->select('products.*');
        $this->qb->join('companies_products', 'companies_products.id_product = products.id');
        $this->qb->where('companies_products.id_company', $this->row->id);
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('products.name', $keyword);
        }
        $this->qb->groupBy('products.id');
        if ($get->entries) {
            $all = (is_numeric($get->entries) ? models(RealProducts::class)->allWithPaging(null, $get->entries) : models(RealProducts::class)->all());
        } else {
            $all = models(RealProducts::class)->allWithPaging();
        }

        return $all;
    }

    // ------------------------------------------------------------------------

    /**
     * Places::contact
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function contact()
    {
        $data = $this->hasMany(Contacts::class, 'id_company');
        if (count($data)) {
            return $data->first();
        }
        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Places::followers
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function followers()
    {
        return $this->hasManyThrough(Members::class, Followers::class, 'id_company', 'id_member');
    }

    // ------------------------------------------------------------------------

    /**
     * Places::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata($input = null)
    {
        if ($input) {
            $this->qb->where('name', $input);
        }
        if($result = $this->hasMany(Metadata::class, 'id_company')) {
            $metadata = new SplArrayObject();
            foreach($result as $row) {
                $metadata->offsetSet($row->name, $row->content);
            }

            if (count($metadata)) {
                return $metadata;
            }

            return null;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Places::image
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function image()
    {
        $this->qb->where('name', 'photo');
        if($result = $this->hasOne(Metadata::class, 'id_company')) {
            if (is_file($filePath = PATH_STORAGE . 'images/companies/' . $result->content)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }

        return storage_url('/images/default/no-image.jpg');
    }

    // ------------------------------------------------------------------------

    /**
     * Places::posts
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function posts()
    {
        return $this->hasManyThrough(\App\Api\Modules\Posts\Models\Posts::class, Posts::class, 'id_company', 'id_post');
    }

    // ------------------------------------------------------------------------

    /**
     * Places::relationships
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function relationships()
    {
        return $this->hasManyThrough(Members::class, Relationships::class, 'id_plan', 'id_member');
    }

    // ------------------------------------------------------------------------

    /**
     * Places::reviews
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'id_company');
    }

    // ------------------------------------------------------------------------

    /**
     * Places::settings
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function settings()
    {
        if($result = $this->hasMany(Settings::class, 'id_company')) {
            $settings = new SplArrayObject();
            foreach($result as $row) {
                $settings->offsetSet($row->key, $row->value);
            }

            return $settings;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Return::taxonomies
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function taxonomies()
    {
        $data = $this->hasMany(Taxonomies::class, 'id_company');
        if (count($data)) {
            return $data;
        }
        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Places::users
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function users()
    {
        return $this->hasManyThrough(Members::class, Users::class, 'id_company', 'id_member');
    }

    public function companyUser()
    {
        return $this->hasOne(Users::class, 'id_company');
    }


    public function user()
    {
        if($user = $this->companyUser()){
            return $user->user;
        }
        return false;
    }


    public function insert($post)
    {
        if ($post) {
//            if($files = input()->files()['photo']){
//                $filePath = PATH_STORAGE . 'images/companies/';
//                if(!file_exists($filePath)){
//                    mkdir($filePath, 0777, true);
//                }
//
//                $upload = new Uploader();
//                $upload->setPath($filePath);
//                $upload->process('photo');
//
//                if ($upload->getErrors()) {
//                    $errors = new Unordered();
//
//                    foreach ($upload->getErrors() as $code => $error) {
//                        $errors->createList($error);
//                    }
//                    $this->output->send([
//                        'error'  => $errors
//                    ]);
//                } else {
//                    $filename = $upload->getUploadedFiles()->first()['name'];
//                    $post['photo'] = $filename;
//                }
//            }
            $metadata = $post['meta'];
            unset($post['meta']);

            $post['code'] = Uid::generate(5);
            if (parent::insert($post)) {
                $id_company = $this->getLastInsertId();
                if (count($metadata)) {
                    foreach ($metadata as $key => $value) {
                        if ( ! models(Metadata::class)->insertOrUpdate([
                            'id_company' => $id_company,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ],[
                            'id_company' => $id_company,
                            'name' => $key
                        ])) {
                            $this->delete($id_company);
                            models(Metadata::class)->deleteManyBy(['id_company' => $id_company]);
                            return false;
                            break;
                        }
                    }
                }
                return ['status' => true, 'id_company' => $id_company];
            } else {
                return false;
            }
        }
    }

    public function update(array $post, array $conditions = [])
    {
        if ($post) {
            $metadata = $post['meta'];
            unset($post['meta']);

            $post['code'] = Uid::generate(5);

            if (parent::update($post, $conditions)) {
                $id_company = $post['id'];
                if (count($metadata)) {
                    foreach ($metadata as $key => $value) {
                        if ( ! models(Metadata::class)->insertOrUpdate([
                            'id_company' => $id_company,
                            'name' => $key,
                            'content' => $value,
                            'record_create_timestamp' => timestamp()
                        ],[
                            'id_company' => $id_company,
                            'name' => $key
                        ])) {
                            return false;
                            break;
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    public function filter($get)
    {
        $this->appendColumns = [
            'category'
        ];

        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('name', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging(null, 12);
        }

        return $all;
    }

    public function city()
    {
        $rajaongkir = new Rajaongkir();
        return $rajaongkir->result->getCity($this->row->id_geodirectory);
    }

    public function statistics()
    {
        $row = $this->row;
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
                $status = $this->qb->select('transactions_logs.*')
                ->from('transactions_logs')
                ->join('transactions', 'transactions.id = transactions_logs.id_transaction')
                ->join('companies_transactions', 'companies_transactions.id_transaction = transactions.id')
                ->where('companies_transactions.id_company', $row->id)
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
}
