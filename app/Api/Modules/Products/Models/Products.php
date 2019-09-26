<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Products\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use App\Api\Modules\Master\Models\Currencies;
use App\Api\Modules\Companies\Models\Products as CompaniesProducts;
/**
 * Class Products
 * @package App\Api\Modules\Products\Models
 */
class Products extends Model
{
    /**
     * Products::$table
     *
     * @var string
     */
    public $table = 'products';

    /**
     * Products::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_product_category',
        'id_currency',
        'name',
        'slug',
        'description',
        'price_capital',
        'price_sale',
        'sku',
        'stock',
        'condition',
        'deliverable',
        'downloadable',
        'target_gender',
        'target_age',
        'visibility'
    ];

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
        $this->qb->where('metadata', 'main');
        if ($data = $this->hasOne(Images::class, 'id_product')) {
            $filePath = PATH_STORAGE . 'images/products/'.$data->filename;
            if (is_file($filePath)) {
                return storage_url($filePath);
            }
            return storage_url('/images/default/no-image.jpg');
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

    public function merchant()
    {
        if ($data = $this->hasOne(CompaniesProducts::class, 'id_product')) {
            if ($data->company) {
                return $data->company;
            }
        }
        return false;
    }

    public function companies_products_all($get=null)
    {
        if ($company = globals()->account->company) {
            $this->qb->select('products.*');
            $this->qb->join('companies_products', 'companies_products.id_product = products.id');
            $this->qb->where('companies_products.id_company', $company->id);
            if ($get) {
                if ($get->period) {
                    $time_data = explode('-', str_replace(' ', '', $get->period));
                    $this->qb->where('DATE(products.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
                    $this->qb->where('DATE(products.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
                }

                if ($keyword = $get->keyword) {
                    $this->qb->like('products.name', $keyword);
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
            $meta = $post['meta'];
            $variant = $post['variant'];
            $wholesale = $post['wholesale'];
            $id_company = $post['id_company'];
            $meta['wholesale'] = serialize($wholesale);
            $post['slug'] = str_replace(' ', '-', strtolower($post['slug']));
            unset($post['meta'], $post['variant'], $post['wholesale'], $post['id_company']);
            if (parent::insert($post)) {
                $id_product = $this->getLastInsertId();
                if ( ! models(CompaniesProducts::class)->insert([
                    'id_company' => $id_company,
                    'id_product' => $id_product
                ])) {
                    return false;
                }
                
                if (count($meta)) {
                    foreach ($meta as $key => $value) {
                        if($metadata = count(models(Metadata::class)->findWhere([
                            'id_product' => $id_product,
                            'name' => $key,
                        ]))){
                            models(Metadata::class)->update([
                                'id_product' => $id_product,
                                'name' => $key,
                                'content' => $value,
                                'id'    => $metadata->id
                            ]);
                        }else{
                            models(Metadata::class)->insert([
                                'id_product' => $id_product,
                                'name' => $key,
                                'content' => $value
                            ]);
                        }
                    }
                }
                $variant = $this->variant(new SplArrayObject($variant), $id_product);
                if ($variant) {
                    foreach ($variant as $data) {
                        $variant_value = $data['value'];
                        unset($data['value']);
                        if (models(Variants::class)->insert($data)) {
                            $id_product_variant = $this->getLastInsertId();
                            if ( ! models(Variants\Metadata::class)->insertOrUpdate([
                                'id_product_variant' => $id_product_variant,
                                'option' => $data['name'],
                                'value' => $variant_value 
                            ], [
                                'id_product_variant' => $id_product_variant,
                                'option' => $data['name'],
                            ])) {
                                return false;
                                break;
                            }
                        } else {
                            return false;
                            break;
                        }
                    }
                }

                if ($files = input()->files()) {
                    foreach ($files as $key => $value) {
                        $metadata = [];
                        $filePath = PATH_STORAGE . 'images/products/';
                        if(!file_exists($filePath)){
                            mkdir($filePath, 0777, true);
                        }

                        $upload = new Uploader();
                        $upload->setPath($filePath);
                        $upload->process($key);

                        if ($upload->getErrors()) {
                            $errors = new Unordered();

                            foreach ($upload->getErrors() as $code => $error) {
                                $errors->createList($error);
                            }
                            $this->output->send([
                                'error'  => $errors
                            ]);
                        } else {
                            $file = $upload->getUploadedFiles()->first();
                            $data = [
                                'id_product' => $id_product,
                                'filename' => $file['name'],
                                'mime' => $file['mime'],
                                'metadata' => $key,
                                'record_create_timestamp' => timestamp()
                            ];
                            models(Images::class)->insert($data);
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            $this->output->send(404);
        }
    }

    public function update($post)
    {
        if ($post) {
            $meta = $post['meta'];
            $variant = $post['variant'];
            $wholesale = $post['wholesale'];
            $id_company = $post['id_company'];
            $meta['wholesale'] = serialize($wholesale);
            unset($post['meta'], $post['variant'], $post['wholesale'], $post['id_company']);
            if (parent::update($post)) {
                $id_product = $post['id'];
                if ( ! models(CompaniesProducts::class)->update([
                    'id_company' => $id_company,
                ],[
                    'id_product' => $id_product
                ])) {
                    return false;
                }
                if (count($meta)) {
                    foreach ($meta as $key => $value) {
                        if ( ! models(Metadata::class)->insertOrUpdate([
                            'id_product' => $id_product,
                            'name' => $key,
                            'content' => $value
                        ], [
                            'id_product' => $id_product,
                            'name' => $key,
                        ])) {
                            $this->delete($id_product);
                            return false;
                            break;
                        }
                    }
                }
                $variant = $this->variant(new SplArrayObject($variant), $id_product);
                if ($variant) {
                    foreach ($variant as $data) {
                        $variant_value = $data['value'];
                        unset($data['value']);
                        if (models(Variants::class)->update($data)) {
                            $id_product_variant = $data['id'];
                            if ( ! models(Variants\Metadata::class)->insertOrUpdate([
                                'id_product_variant' => $id_product_variant,
                                'option' => $data['name'],
                                'value' => $variant_value 
                            ], [
                                'id_product_variant' => $id_product_variant,
                            ])) {
                                return false;
                                break;
                            }
                        } else {
                            return false;
                            break;
                        }
                    }
                }

                if ($files = input()->files()) {

                    foreach ($files as $key => $value) {
                        $metadata = [];
                        $filePath = PATH_STORAGE . 'images/products/';
                        if(!file_exists($filePath)){
                            mkdir($filePath, 0777, true);
                        }

                        $upload = new Uploader();
                        $upload->setPath($filePath);
                        $upload->process($key);

                        if ($upload->getErrors()) {
                            $errors = new Unordered();

                            foreach ($upload->getErrors() as $code => $error) {
                                $errors->createList($error);
                            }
                            $this->output->send([
                                'error'  => $errors
                            ]);
                        } else {
                            $file = $upload->getUploadedFiles()->first();
                            if ($id_product) {
                                $data = models(Images::class)->findWhere(['id_product' => $id_product, 'metadata' => $key]);
                                if ($data) {
                                    $data = $data->first();
                                    if (is_file($image = $filePath.$data->filename)) {
                                        unlink($image);
                                    }
                                }
                            }
                            $data = [
                                'id_product' => $id_product,
                                'filename' => $file['name'],
                                'mime' => $file['mime'],
                                'metadata' => $key,
                                'record_create_timestamp' => timestamp()
                            ];
                            models(Images::class)->insertOrUpdate($data, [
                                'id_product' => $id_product,
                                'metadata' => $key
                            ]);
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            $this->output->send(404);
        }
    }

    public function delete($id)
    {
        $this->appendColumns = ['variants'];
        if ($data = $this->find($id)) {
            models(Metadata::class)->deleteManyBy(['id_product' => $id]);
            if ($data->variants) {
                foreach ($data->variants as $variant) {
                    models(Variants\Metadata::class)->deleteManyBy(['id_product_variant' => $variant->id]);
                }
            }
            models(Variants::class)->deleteManyBy(['id_product' => $id]);
            $images = models(Images::class)->findWhere(['id_product' => $id]);
            if ($images) {
                $filePath = PATH_STORAGE . 'images/products/';
                foreach ($images as $image) {
                    if (is_file($file = $filePath.$image->filename)) {
                        unlink($file);
                    }
                }
            }
            models(Images::class)->deleteManyBy(['id_product' => $id]);
            models(CompaniesProducts::class)->deleteManyBy(['id_product' => $id]);
            if (parent::delete($id)) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    protected function variant($variant = null, $id_product) {
        if ($variant) {
            $variant_data = [];
            foreach ($variant['name'] as $key => $value) {
                if ($variant['sku'][$key] != '' && $id_product != null) {
                    $variant_data[$key] = [
                        'id' => ($variant['id'][$key] ? $variant['id'][$key] : ''),
                        'id_product' => $id_product,
                        'name' => $variant['name'][$key],
                        'value' => $variant['value'][$key],
                        'price_additional' => $variant['price_additional'][$key],
                        'sku' => $variant['sku'][$key],
                        'stock' => $variant['stock'][$key],
                    ];
                }
            }
            if (count($variant_data)) {
                return $variant_data;
            }
            return null;
        } else {
            return null;
        }
    }

    public function company()
    {
        if($company = $this->hasOne(\App\Api\Modules\Companies\Models\Products::class, 'id_product')){
            return $company->company;
        }
        return false;
    }

    public function filter($get)
    {
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
            $all = $this->allWithPaging();
        }

        return $all;
    }

    public function list_filter($get)
    {
        $system = system_limit_pagination_posts();
        if (is_numeric($id_product_category = $get->id_product_category)) {
            $this->qb->where('id_product_category', $id_product_category);
        }

        if ($search = $get->search) {
            $this->qb->like('name', $search);
        }
        if ($limit = $system) {
            if ($get->entries) {
                $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
            } else {
                $all = $this->allWithPaging(null, $limit);
            }
        } else {
            $all = $this->allWithPaging();
        }
        return $all;
    }    
}