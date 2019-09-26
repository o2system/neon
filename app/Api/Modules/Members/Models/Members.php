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
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Security\Generators\Uid;
use App\Api\Modules\Testimonials\Models\Testimonials;
use App\Api\Modules\Companies\Models\Members as CompaniesMembers;
use App\Libraries\Rajaongkir;
use App\Api\Modules\Transactions\Models\Transactions as RealTransactions;

/**
 * Class Members
 * @package App\Api\Modules\Members\Models
 */
class Members extends Model
{
	use RelationTrait;

    /**
     * Members::$table
     *
     * @var string
     */
    public $table = 'members';

    /**
     * Members::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
    	'id',
    	'fullname',
    	'number',
    ];

    /**
     * Members::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
    	'image',
    	'record',
        'metadata',
        'user',
        'statistic',
    ];

    // ------------------------------------------------------------------------

    /**
     * Members::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata($input = null)
    {
    	if ($input) {
    		$this->qb->where('name', $input);
    	}
    	if($result = $this->hasMany(Metadata::class, 'id_member')) {
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

    public function image()
    {
        $this->qb->where('name', 'photo');
        if($result = $this->hasOne(Metadata::class, 'id_member')) {
            if (is_file($filePath = PATH_STORAGE . 'images/members/' . $result->content)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
        }

        return storage_url('/images/default/no-image.jpg');
    }

    public function testimony()
    {
        return $this->hasOne(Testimonials::class, 'id_member');
    }

    public function companies_members_all($get=null)
    {
        if ($company = globals()->account->company) {
            $this->qb->select('members.*');
            $this->qb->join('companies_members', 'companies_members.id_member = members.id');
            $this->qb->where('companies_members.id_company', $company->id);
            if ($get) {
                if ($get->period) {
                    $time_data = explode('-', str_replace(' ', '', $get->period));
                    $this->qb->where('DATE(members.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
                    $this->qb->where('DATE(members.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
                }

                if ($keyword = $get->keyword) {
                    $this->qb->like('members.fullname', $keyword);
                }

                if ($get->entries) {
                    $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
                } else {
                    $all = $this->allWithPaging();
                }

                return $all;
            }
            
            return $this->allWithPaging();
        } else {
            return false;
        }
    }

    public function insert($post)
    {
    	if ($post) {
    		// if($files = input()->files()['photo']){
    		// 	$filePath = PATH_STORAGE . 'images/members/';
    		// 	if(!file_exists($filePath)){
    		// 		mkdir($filePath, 0777, true);
    		// 	}

    		// 	$upload = new Uploader();
    		// 	$upload->setPath($filePath);
    		// 	$upload->process('photo');

    		// 	if ($upload->getErrors()) {
    		// 		$errors = new Unordered();

    		// 		foreach ($upload->getErrors() as $code => $error) {
    		// 			$errors->createList($error);
    		// 		}
    		// 		$this->output->send([
    		// 			'error'  => $errors
    		// 		]);
    		// 	} else {
    		// 		$filename = $upload->getUploadedFiles()->first()['name'];
    		// 		$post['meta']['photo'] = $filename;
    		// 	}
    		// }
    		$post['number'] = Uid::generate(5);
    		$metas = $post['meta'];
            $id_company = null;
            if ($post['id_company']) {
                $id_company = $post['id_company'];
            }
            
    		unset($post['meta'], $post['id_company']);

    		if (parent::insert($post)) {
    			$id_member = $this->getLastInsertId();
                if ($id_company) {
                    if ( ! models(CompaniesMembers::class)->insert([
                        'id_company' => $id_company,
                        'id_member' => $id_member
                    ])) {
                        $this->delete($id_member);
                        return false;
                    }
                }
    			if (count($metas)) {
    				foreach ($metas as $key => $value) {
    					if( ! models(Metadata::class)->insertOrUpdate(
    						[
    							'id_member' => $id_member,
    							'name' => $key,
    							'content' => $value
    						],
    						[
    							'id_member' => $id_member,
    							'name' => $key
    						]
    					)) {
    						$this->delete($id_member);
			                return false;
			                break; // if failed
			            }
    				}
    			}
    			return ['status' => true, 'id_member' => $id_member];
    		} else {
    			return false;
    		}
    	}
    	return false;
    }

    public function update($post, $condition = [])
    {
    	if ($post) {
    		// if($files = input()->files()['photo']){
    		// 	$filePath = PATH_STORAGE . 'images/members/';
    		// 	if(!file_exists($filePath)){
    		// 		mkdir($filePath, 0777, true);
    		// 	}

    		// 	$upload = new Uploader();
    		// 	$upload->setPath($filePath);
    		// 	$upload->process('photo');

    		// 	if ($upload->getErrors()) {
    		// 		$errors = new Unordered();

    		// 		foreach ($upload->getErrors() as $code => $error) {
    		// 			$errors->createList($error);
    		// 		}
    		// 		$this->output->send([
    		// 			'error'  => $errors
    		// 		]);
    		// 	} else {
    		// 		if ($post['id']) {
      //                   $data = $this->find($post['id']);
      //                   if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
      //                       unlink($image);
      //                   }
      //               }
    		// 		$filename = $upload->getUploadedFiles()->first()['name'];
    		// 		$post['meta']['photo'] = $filename;
    		// 	}
    		// }
    		$metas = $post['meta'];
            $id_company = null;
            if ($post['id_company']) {
                $id_company = $post['id_company'];
            }
    		unset($post['meta'], $post['id_company']);

    		if (parent::update($post)) {
    			$id_member = $post['id'];
                if ($id_company) {
                    if ( ! models(CompaniesMembers::class)->update([
                        'id_company' => $id_company
                    ],[
                        'id_member' => $id_member
                    ])) {
                        $this->delete($id_member);
                        return false;
                    }
                }
    			if (count($metas)) {
    				foreach ($metas as $key => $value) {
    					if( ! models(Metadata::class)->insertOrUpdate(
    						[
    							'id_member' => $id_member,
    							'name' => $key,
    							'content' => $value
    						],
    						[
    							'id_member' => $id_member,
    							'name' => $key
    						]
    					)) {
			                return false;
			                break; // if failed
			            }
    				}
    			}
    			return true;
    		} else {
    			return false;
    		}
    	}
    	return false;
    }

    // public function delete($id=null)
    // {
    // 	if ($id) {
    // 		models(Metadata::class)->deleteManyBy(['id_member' => $id]);
    // 		if (parent::delete($id)) {
    // 			return true;
    // 		}
    // 		return false;
    // 	} else {
    // 		$this->output->send(404);
    // 	}
    // }

    public function user()
    {
        if($memberUser = $this->hasOne(Users::class, 'id_member')){
            return $memberUser->user;
        }
        return false;
    }

    public function transactions()
    {
        $this->qb->orderBy('id_transaction', 'DESC');
        return $this->hasMany(Transactions::class,
            'id_member');
    }

    public function onShoppingCarts()
    {
        $results = new SplArrayObject();
        if($transactions = $this->transactions()){
            if($transactions->count()){
                foreach ($transactions as $key =>  $transaction){
                    if($transaction = $transaction->transaction){
                        if($latestLog = $transaction->latestLog){
                            if($latestLog->status === 'ON_SHOPPING_CART'){
                                $results->offsetSet($key, $transaction);
                            }
                        }
                    }
                }
            }
        }
        return $results;
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
                $status = $this->qb
                ->from('members_transactions')
                ->join('transactions', 'transactions.id = members_transactions_id_transaction')
                ->join('transactions_logs','transactions_logs.id_transaction = transactions.id')
                ->where('members_transactions.id_member', $row->id)
                ->where('transactions_logs.status', $key)
                ->get();
            $result->offsetSet($no++, new SplArrayObject([
                'status' => $key,
                'total' => count($status),
                'icon' => $value
            ]));
        }
        return $result;
    }

    public function filter($get)
    {
        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('fullname', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging();
        }

        return $all;
    }

    public function city()
    {
        if ($this->row->id_geodirectory) {
            $rajaongkir = new Rajaongkir();
            return $rajaongkir->result->getCity($this->row->id_geodirectory);
        }
        return false;
    }

    public function transactions_filter($get)
    {
        $this->qb->select(models(RealTransactions::class)->table.'.*');
        $this->qb->join('members_transactions', 'members_transactions.id_transaction = '.models(RealTransactions::class)->table.'.id');
        $this->qb->join('products', 'products.id = '.models(RealTransactions::class)->table.'.reference_id');
        $this->qb->where('members_transactions.id_member', $this->row->id);

        if ($get->period) {
            $time_data = explode('-', str_replace(' ', '', $get->period));
            $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
            $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
        }

        if ($keyword = $get->keyword) {
            $this->qb->like('products.name', $keyword);
        }

        $this->qb->groupBy('members_transactions.id');

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? models(RealTransactions::class)->allWithPaging(null, $get->entries) : models(RealTransactions::class)->all());
        } else {
            $all = models(RealTransactions::class)->allWithPaging();
        }

        return $all;

        
    }
}