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

namespace App\Api\Modules\Testimonials\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Members\Models\Members;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Testimonials
 * @package App\Api\Modules\Testimonials\Models
 */
class Testimonials extends Model
{
	use RelationTrait;

	/**
	 * Testimonials::$table
	 *
	 * @var string
	 */
	public $table = 'testimonials';

	/**
	 * Testimonials::$visibleColumns
	 *
	 * @var array
	 */
	public $visibleColumns = [
		'id',
		'id_member',
		'comment',
		'rate',
	];

	/**
	 * Testimonials::$appendColumns
	 *
	 * @var array
	 */
	public $appendColumns = [
		'metadata',
		'member'
	];

	public  function member(){
		return $this->belongsTo(Members::class,'id_member');
	}

	public function members_total_testimonials()
	{
		if ($member = globals()->account->member) {
			if ($total = count($this->findWhere(['id_member' => $member->id]))) {
				return $total;
			}
		}
		return 0;
	}

	public function filter($get)
	{
		$this->qb->select('testimonials.*')
		->join('members', 'members.id = testimonials.id_member');
		
		if ($get->period) {
			$time_data = explode('-', str_replace(' ', '', $get->period));
			$this->qb->where('DATE(testimonials.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
			$this->qb->where('DATE(testimonials.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
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
}
