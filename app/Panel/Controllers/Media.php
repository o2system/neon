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

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Kernel\Http\Message\UploadFile;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Media
 * @package App\Panel\Controllers
 */
class Media extends AuthorizedController
{
    /**
     * Media::$model
     *
     * @var string|\O2System\Framework\Models\Sql\System\Media
     */
    public $model = 'O2System\Framework\Models\Sql\System\Media';

    // ------------------------------------------------------------------------

    /**
     * Media::index
     */
    public function index()
    {
        if ($keyword = input()->get('keyword')) {
             $this->model->qb->like('label', $keyword);
        }

        if ($type = input()->get('type')) {
            $this->model->qb->where('record_type', $type);
        }
        
        view('media/index', [
            'segment' => '',
            'medias' => $this->model->allWithPaging(),
            'space' => new SplArrayObject([
                'total' => $spaceTotal = disk_total_space(PATH_STORAGE),
                'used' => $spaceUsed = (new SplDirectoryInfo(PATH_STORAGE))->getSize(),
                'percentage' => round(($spaceUsed / $spaceTotal) * 100, 5)
            ])
        ]);
    }

    /**
     * Media::popup
     */
    public function popup()
    {
        presenter()->theme->setLayout('blank');

        if ($keyword = input()->get('keyword')) {
             $this->model->qb->like('label', $keyword);
        }

        if ($type = input()->get('type')) {
            $this->model->qb->where('record_type', $type);
        }
        
        view('media/index', [
            'segment' => '/popup',
            'medias' => $this->model->allWithPaging(),
            'space' => new SplArrayObject([
                'total' => $spaceTotal = disk_total_space(PATH_STORAGE),
                'used' => $spaceUsed = (new SplDirectoryInfo(PATH_STORAGE))->getSize(),
                'percentage' => round(($spaceUsed / $spaceTotal) * 100, 5)
            ])
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Media::upload
     */
    public function upload()
    {
        /** Text Handler */
        if ($post = input()->post()) {
            
            /** Document, audio */
            if ($post['url']) {
                $_POST = [
                    'label' => $post['label'],
                    'filepath' => $post['url'],
                    'size' => 1,
                    'mime' => 'files/docs',
                    'record_type' => $post['record_type'],
                ];
            }

            /** Video. */
            if ($post['videoID']) {
                $_POST = [
                    'label' => $post['label'],
                    'filepath' => $post['videoID'],
                    'size' => 1,
                    'mime' => 'video/file',
                    'record_type' => 'VIDEO',
                ];
            }
            
            $this->model->insert($post);
            
            redirect_url($_SERVER['HTTP_REFERER']);
        }

        /** File Handler */ 
        if ($files = input()->files()) {
            foreach($files as $file) {
                if ($file instanceof UploadFile) {
                    if(in_array($file->getExtension(), ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'webm'])) {
                        $file->setPath($filePath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR );
                    } else {
                        $file->setPath($filePath = PATH_STORAGE . 'files' . DIRECTORY_SEPARATOR );
                    }

                    if ($file->store()) {
                        $_POST = [
                            'label' => $file->getName(),
                            'filepath' => str_replace([PATH_STORAGE, '\\'], ['', '/'], $filePath) . $file->getName(),
                            'size' => $file->getSize(),
                            'mime' => $file->getFileMime(),
                            'record_type' => 'IMAGE',
                        ];

                        $this->model->insert(input()->post());

                        redirect_url(base_url('panel/media'));
                    } else {
                        output()->sendPayload([
                            'message' => $file->getError()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Media::form
     */
    public function form()
    {
       if ($input = input()->post()) {
            
            if ($post = $this->model->find($input['id'])) {
                $this->model->update($input);
            }
            
            redirect_url(base_url('panel/media'));
        }
    }

    /**
     * Media::delete
     */
    public function delete($id)
    {
    	if ($media = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	]));
    	}

		redirect_url(base_url('panel/media'));
    }
}