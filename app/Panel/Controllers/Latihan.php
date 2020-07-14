<?php
/**
 * This file is part of the WebIn Platform.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Models\Sql\System\Modules;

/**
 * Class LatihanMorph
 * @package App\Panel\Controllers\Store
 */
class Latihan extends AuthorizedController
{
    /**
     * @var string
     */
    protected $model = 'App\Models\Posts';
    
    public function index()
    {
        $module = models(Modules::class)->find('App\\Site', 'namespace');
        //print_out(models(Modules::class)->db->getLastQuery());

        $_POST = [
            'settings' => [
                'site_title' => 'Hello'
            ]
        ];

        models(Modules::class)->update(input()->post(), [
            'namespace' => 'App\\Site'
        ]);

        print_out($module);
        // $this->model->qb->select('posts.*');
        // $this->model->qb->where('record_type', 'SAMPLE');
        // $this->model->qb->join('sys_metadata', 'sys_metadata.ownership_id = CONCAT(posts.id, "-", posts.record_language) AND sys_metadata.ownership_model = "App\\\Api\\\Models\\\Posts"');
        // $this->model->qb->bracketOpen()
        //                 ->where('sys_metadata.name', 'foo')
        //                 ->whereIn('sys_metadata.content', [1, 2])
        //                 ->bracketClose();
                        
        //print_code($this->model->all());
        //print_code($this->model->db->getQueries(), true);
        //print_code($this->model->all()->first()->metadata, true);
        
        //$this->model->qb->where('sys_metadata.content', '1');
        $_POST = [
            'id' => 14,
            'title' => 'Something Update',
            'slug' => 'something',
            'excerpt' => 'something',
            'content' => 'something',
            'record_type' => 'SAMPLE',
            'record_language' => 'id-ID',
            'metadata' => [
                'foo' => 'barbarbarbar',
                'hello' => 'world'
            ],
            'settings' => [
                'commentable' => 'NO'
            ]
        ];

        //$this->model->insert($this->input->post());
        //exit;

        $this->model->update($this->input->post());
        
        exit;

        if ($keyword = input()->get('keyword')) {
            $this->model->qb->like('title', $keyword);
        }

        if ($status = input()->get('status')) {
            $this->model->qb->where('record_status', $status);
        }

        $this->model->qb->where('record_type', 'PRODUCT');
    }
}