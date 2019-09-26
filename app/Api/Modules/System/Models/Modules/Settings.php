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
namespace App\Api\Modules\System\Models\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Settings
 * @package App\Api\Modules\System\Models\Modules
 */
class Settings extends Model
{
    /**
     * Settings::$table
     *
     * @var string
     */
    public $table = 'sys_modules_settings';

    /**
     * Settings::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
    	'id',
    	'id_sys_module',
    	'key',
    	'value',
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
    	return $this->belongsTo(Modules::class, 'id_sys_module');
    }

    public function fetch($input = null)
    {
    	if ($input) {
    		$this->qb->where('key', $input);
    	}
    	if($result = $this->all()) {
    		$metadata = new SplArrayObject();
    		foreach($result as $row) {
    			$metadata->offsetSet($row->key, $row->value);
    			if($row->key == 'site_logo'){
                    $file = PATH_STORAGE.'images/settings/'.$row->value;
                    if (is_file($file)) {
                        $metadata->offsetSet('logo', storage_url($file));
                    }
                }elseif($row->key == 'app_manifest_icon'){
                    $file = PATH_STORAGE.'images/settings/'.$row->value;
                    if (is_file($file)) {
                        $metadata->offsetSet('logo_manifest', storage_url($file));
                    }
                }
    		}

    		if (count($metadata)) {
    			return $metadata;
    		}
    		return new SplArrayObject();            
    	}

    	return new SplArrayObject();
    }

    public function site_logo()
    {
        $fetch = $this->fetch('site_logo');
        if ($logo = $fetch->site_logo) {
            $file = PATH_STORAGE.'images/settings/'.$logo;
            if (is_file($file)) {
                return storage_url($file);
            }
            return storage_url('/images/default/no-image.jpg');
        }
        return storage_url('/images/default/no-image.jpg');
    }


    public function logo()
    {
        $fetch = $this->fetch('site_logo');
        if ($logo = $fetch->site_logo) {
            $file = PATH_STORAGE.'images/settings/'.$logo;
            if (is_file($file)) {
                return storage_url($file);
            }
            return storage_url('/images/default/no-image.jpg');
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function app_manifest_icon()
    {
        $fetch = $this->fetch('app_manifest_icon');
        if ($logo = $fetch->app_manifest_icon) {
            $file = PATH_STORAGE.'images/settings/'.$logo;
            if (is_file($file)) {
                return storage_url($file);
            }
            return storage_url('/images/default/no-image.jpg');
        }
        return storage_url('/images/default/no-image.jpg');
    }
    public function manifest_logo()
    {
        $fetch = $this->fetch('app_manifest_icon');
        if ($logo = $fetch->app_manifest_icon) {
            $file = PATH_STORAGE.'images/settings/'.$logo;
            if (is_file($file)) {
                return storage_url($file);
            }
            return storage_url('/images/default/no-image.jpg');
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function insertOrUpdate(array $sets, array $conditions = []) 
    {
        if ($sets['id_sys_module'] == null) {
            $id_sys_module = $sets['id_sys_module'] = 1;
            unset($sets['id_sys_module']);
        }
		foreach($sets as $key => $value) {
		if( ! parent::insertOrUpdate(['id_sys_module' => ($id_sys_module == null ? 1 : $id_sys_module ),'key' => $key,'value' => $value], ['key' => $key])) {
                break; // if failed
                return false;
            }
        }
        return true;
    }
}